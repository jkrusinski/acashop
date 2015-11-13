<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Show All Products
     * @return Response
     */
    public function showAllProductsAction()
    {
        $db = $this->get('acadb');

        $query = "SELECT * FROM aca_product";
        $results = $db->fetchRowMany($query);

        return $this->render(
            'AcaShopBundle:Products:products.html.twig',
            array(
                'results' => $results
            )
        );
    }

    /**
     * Product Detail Page
     * @param string $slug
     * @param bool $error
     * @return Response
     */
    public function showProductAction($slug, $error)
    {
        $db = $this->get('acadb');

        $query = "SELECT * FROM aca_product WHERE slug = :mySlug";

        $result = $db->fetchRow($query, array('mySlug' => $slug));

        if($error) $msg = 'Please make sure quantity is an integer.';
        else $msg = null;

        return $this->render(
            'AcaShopBundle:Products:product.detail.html.twig',
            array(
                'product' => $result,
                'msg' => $msg
            )
        );
    }
}