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
        if (!$this->session->isStarted()) $this->session->start();
    }

    /**
     * Create a cart record if it doesn't exist and return the ID
     * If it does exist, just return the ID
     * @param string $userID
     * @return int $cartID
     */
    public function getCartID($userID)
    {
        $cartID = null;

        // check if cart exists
        $query = "SELECT * FROM aca_cart WHERE user_id = :userID";
        $exist = $this->db->fetchRow(
            $query,
            array('userID' => $userID)
        );

        if (!$exist) {

            $cartID = $this->db->insert(
                'aca_cart',
                array('user_id' => $userID)
            );

        } else {

            $cartID = $exist['id'];

        }

        return $cartID;

    }

    /**
     * Add a product to the cart
     * @param int $productID
     * @param int $qty
     * @return int $cartProductID
     */
    public function addProductToCart($productID, $qty)
    {
        // fetch product information
        $query = "SELECT * FROM aca_product WHERE id = :id";
        $product = $this->db->fetchRow($query, array('id' => $productID));

        $productPrice = $product['price'];
        $cartID = $this->session->get('cartID');

        // insert into aca_cart_product
        $cartProductID = $this->db->insert(
            'aca_cart_product',
            array(
                'cart_id' => $cartID,
                'product_id' => $productID,
                'unit_price' => $productPrice,
                'quantity' => $qty
            )
        );

        return $cartProductID;
    }

    /**
     * Returns all products in a cart
     * @return array|null
     */
    public function getAllCartProducts()
    {
        $cartID = $this->session->get('cartID');

        $query = "
            SELECT aca_cart_product.id, aca_cart_product.unit_price, aca_cart_product.quantity,
                aca_product.id AS product_id, aca_product.name, aca_product.image
            FROM aca_cart_product
            INNER JOIN aca_product
            ON aca_cart_product.product_id = aca_product.id
            WHERE aca_cart_product.cart_id = :cartID
        ";

        $result = $this->db->fetchRowMany($query, array('cartID' => $cartID));

        return $result;
    }

    public function removeProductFromCart()
    {

    }

    public function updateProductQty()
    {

    }

}