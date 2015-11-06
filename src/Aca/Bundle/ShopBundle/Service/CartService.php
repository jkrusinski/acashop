<?php

namespace Aca\Bundle\ShopBundle\Service;

use Simplon\Mysql\Mysql;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{
    /**
     * Database class
     * @var Mysql
     */
    protected $db;

    /**
     * Session object
     * @var Session
     */
    protected $session;

    /**
     * Construct service with the database connection.
     * @param Mysql $db
     * @param Session $session
     */
    public function __construct(Mysql $db, Session $session)
    {
        $this->db = $db;
        $this->session = $session;

        //start session if not already
        if(!$this->session->isStarted()) $this->session->start();
    }

    /**
     * Create a cart record if it doesn't exist and return the ID
     * If it does exist, just return the ID
     */
    public function getCartID()
    {
        // check if cart exists
        $existQuery = "SELECT * FROM aca_cart WHERE user_id = :userID";
        $exist = $this->db->fetchRow(
            $existQuery,
            array('userID' => $this->session->get('userID'))
        );

        echo '<pre>';

        if(!$exist) {
            $result = $this->db->insert(
                'aca_cart',
                array('user_id' => $this->session->get('userID'))
            );

            return $result;

        } else {

            return $exist['id'];

        }

    }

    /**
     * Add a product to the cart
     * @param int $productID
     * @param int $qty
     * return bool
     */
    public function addProductToCart($productID, $qty)
    {

    }

    public function getAllCartProducts()
    {

    }

    public function removeProductFromCart()
    {

    }

    public function updateProductQty()
    {

    }

}