<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Aca\Bundle\ShopBundle\Db\Database;

class ProductController extends Controller
{
    /**
     * Show all products
     * @return Response
     */
    public function showAllProductsAction()
    {
        $db = new Database();

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

        $db = new Database();

        $query = "SELECT * FROM aca_product WHERE slug='$slug'";
        $result = $db->fetchRowMany($query);

        $result = $result[0];

        return $this->render(
            'AcaShopBundle:Products:product.detail.html.twig',
            array(
                'product' => $result
            )
        );
    }
}