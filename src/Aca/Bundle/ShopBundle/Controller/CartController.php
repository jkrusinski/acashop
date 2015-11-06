<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CartController extends Controller
{
    public function showCartAction()
    {
        return $this->render(
            'AcaShopBundle:Cart:show.cart.html.twig'
        );
    }

    public function addToCartAction(Request $request)
    {

        $productID = $request->get('product-id');
        $qty = $request->get('qty');

        $cart = $this->get('cart');


        return new RedirectResponse('/cart');
    }
}