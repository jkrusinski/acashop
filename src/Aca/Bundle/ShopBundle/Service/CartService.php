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
        $query = "SELECT price FROM aca_product WHERE id = :id";
        $result = $this->db->fetchRow($query, array('id' => $productID));

        $productPrice = $result['price'];
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
            SELECT
                cp.id, cp.unit_price, cp.quantity, p.id AS product_id, p.name, p.image, p.slug
            FROM
                aca_cart_product AS cp
                INNER JOIN aca_product AS p ON cp.product_id = p.id
            WHERE cp.cart_id = :cartID
        ";

        $result = $this->db->fetchRowMany($query, array('cartID' => $cartID));

        return $result;
    }

    /**
     * Deletes an item from the cart.
     * @param int $cartProductID
     * @return bool
     * @throws \Simplon\Mysql\MysqlException
     */
    public function removeProductFromCart($cartProductID)
    {
        return $this->db->delete(
            'aca_cart_product',
            array('id' => $cartProductID)
        );
    }

    /**
     * Updates the quantity of a product in the cart.
     * @param int $cartProductID
     * @param int $newQty
     * @return bool
     * @throws \Simplon\Mysql\MysqlException
     */
    public function updateProductQty($cartProductID, $newQty)
    {
        return $this->db->update(
            'aca_cart_product',
            array('id' => $cartProductID),
            array('quantity' => $newQty)
        );
    }

    /**
     * Gets the subtotal of the cart.
     * @param array $results
     * @return int
     */
    public function getTotal($results = array())
    {
        // get $results if it is not passed through
        if (empty($results)) $results = $this->getAllCartProducts();

        $sum = 0;

        foreach ($results as $item) {
            $sum += $item['unit_price'] * $item['quantity'];
        }

        return $sum;
    }

}