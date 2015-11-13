<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    /**
     * Shows item in the cart.
     * @return Response
     */
    public function showCartAction()
    {
        // if not logged in, direct to login page
        if (!$this->isLoggedIn()) return $this->redirectToRoute('aca_login_handler', array('error' => true));

        return $this->buildCart();
    }

    /**
     * Update the quantity of a cart item.
     * Then gets products and rebuilds cart.
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateQtyAction(Request $request)
    {
        $cartProductID = (int)$request->get('cart-product-id');
        $newQty = (int)$request->get('new-qty');
        $oldQty = (int)$request->get('old-qty');

        // if input valid, update product qty
        if ($newQty == $oldQty) {

            // no need for sql query
            $msg = 'Cart updated successfully.';

        } else if ($newQty > 0) {

            $this->get('cart')->updateProductQty($cartProductID, $newQty);
            $msg = 'Cart updated successfully.';

        } else {

            $msg = 'Please make sure quantity is an integer.';

        }

        return $this->buildCart(true, $msg);
    }

    /**
     * Adds an item to the shopping cart.
     * @param Request $request
     * @return RedirectResponse
     * @todo Make addToCart update quantity if product already exists in cart
     */
    public function addToCartAction(Request $request)
    {
        // if not logged in, direct to login page
        if (!$this->isLoggedIn()) return $this->redirectToRoute(
            'aca_login_handler',
            array('error' => true)
        );

        // (int) casting will make value int(0) if string is not numeric
        $qty = (int)$request->get('qty');
        $slug = $request->get('slug');

        // if quantity is invalid, return to product detail page with error
        if (!$qty) return $this->redirectToRoute(
            'aca_show_product_detail',
            array('slug' => $slug, 'error' => true)
        );

        // add to cart
        $productID = $request->get('product-id');
        $cart = $this->get('cart');
        $cart->addProductToCart($productID, $qty);

        // after item added, return to cart
        return $this->redirectToRoute('aca_show_cart');
    }

    /**
     * Deletes an item from the shopping cart.
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteFromCartAction(Request $request)
    {
        $cartProductID = $request->get('cart-product-id');
        $this->get('cart')->removeProductFromCart($cartProductID);

        return $this->redirectToRoute('aca_show_cart');
    }

    /**
     * Builds a response object containing the shopping cart.
     * @param bool|false $updated
     * @param string $msg
     * @return Response
     */
    public function buildCart($updated = false, $msg = '')
    {
        // put array of products into $results
        $cart = $this->get('cart');
        $results = $cart->getAllCartProducts();

        // if cart empty
        if (!$results) {

            $empty = true;
            $sum = null;

        } else {

            $empty = false;
            $sum = $cart->getTotal($results);

        }



        return $this->render(
            'AcaShopBundle:Cart:show.cart.html.twig',
            array(
                'updated' => $updated,
                'results' => $results,
                'empty' => $empty,
                'updateMsg' => $msg,
                'sum' => $sum
            )
        );
    }

    /**
     * Make sure user is logged in to do cart work
     * @todo add this to a login service
     * @return bool
     */
    public function isLoggedIn()
    {
        $session = $this->get('session');

        $loggedIn = $session->get('loggedIn');

        return (bool)$loggedIn;
    }
}