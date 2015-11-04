<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Show all products
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

    public function showProductAction($slug)
    {
        $db = $this->get('acadb');

        $query = "SELECT * FROM aca_product WHERE slug = :mySlug";

        $result = $db->fetchRow($query, array('mySlug' => $slug));

        return $this->render(
            'AcaShopBundle:Products:product.detail.html.twig',
            array(
                'product' => $result
            )
        );
    }
}