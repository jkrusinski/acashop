<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends Controller
{
    public function showCartAction()
    {
        $cart = $this->get('cart');
        $results = $cart->getAllCartProducts();

        return $this->render(
            'AcaShopBundle:Cart:show.cart.html.twig',
            array('results' => $results)
        );
    }

    public function addToCartAction(Request $request)
    {
        $productID = $request->get('product-id');
        $qty = $request->get('qty');

        $cart = $this->get('cart');
        $cart->addProductToCart($productID, $qty);

        return new RedirectResponse('/cart');
    }
}