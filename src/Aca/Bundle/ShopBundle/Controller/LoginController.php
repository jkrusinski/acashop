<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function loginAction(Request $request)
    {
        $username = strtolower($request->get('username')); // make username case insensitive
        $password = $request->get('password');

        $session = $this->setUserSession($username, $password);

        dump($session);

        $loggedIn = $session->get('loggedIn');
        $name = $session->get('name');
        $msg = $session->get('msg');

        return $this->render(
            'AcaShopBundle:LoginForm:login.html.twig',
            array(
                'loggedIn' => $loggedIn,
                'name' => $name,
                'msg' => $msg,
                'username' => $username,
                'password' => $password
            )
        );
    }

    public function logoutAction()
    {
        $session = $this->getSession();

        $session->remove('loggedIn');
        $session->remove('username');
        $session->remove('userID');
        $session->remove('name');
        $session->remove('cartID');
        $session->remove('msg');

        $session->save();

        return new RedirectResponse('/login');
    }

    public function registerAction()
    {
        return $this->render(
            'AcaShopBundle:LoginForm:register.html.twig'
        );
    }

    public function addAccountAction(Request $request)
    {
        // get & set variables
        $msg = [];

        $name = $request->get('name');
        $username = strtolower($request->get('username')); // make username case insensitive
        $password = $request->get('password');
        $confirmPassword = $request->get('confirm-password');

        // make sure all fields are filled in
        $formEmpty = empty($name) || empty($username) || empty($password);

        // if form empty, reload register page with alert
        if($formEmpty) return $this->render(
            'AcaShopBundle:LoginForm:register.html.twig',
            array(
                'formEmpty' => true,
                'name' => $name,
                'username' => $username,
                'password' => $password,
                'confirmPassword' => $confirmPassword
            )
        );

        // validate form information
        $userCheck = $this->checkUsername($username);
        $passwordValid = strlen($password) < 8 || !preg_match('/[A-Z]+[a-z]+[0-9]+/', $password);

        if($userCheck) $msg['user'] = 'Username already exists.';

        if($passwordValid) $msg['password'] = true;

        if($password != $confirmPassword) $msg['confirm'] = 'Please make sure passwords match.';

        // send validation message back to register form
        if(!empty($msg)){

            return $this->render(
                'AcaShopBundle:LoginForm:register.html.twig',
                array(
                    'msg' => $msg,
                    'name' => $name,
                    'username' => $username,
                    'password' => $password,
                    'confirmPassword' => $confirmPassword
                )
            );

        } else { // otherwise create new user

            $db = $this->get('acadb');
            $db->insert(
                'aca_user',
                array(
                    'name' => $name,
                    'username' => $username,
                    'password' => $password
                )
            );

            $session = $this->setUserSession($username, $password);

            return $this->render(
                'AcaShopBundle:LoginForm:addedUser.html.twig',
                array(
                    'name' => $session->get('name'),
                    'username' => $session->get('username')
                )
            );

        }

    }

    /**
     * Starts the session if the session isn't started.
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function getSession()
    {
        $session = $this->get('session');
        if (!$session->isStarted()) $session->start();

        return $session;
    }

    /**
     * Checks to see if username already exists
     * @param string $username Desired username
     * @return bool Returns true if user already exits, false otherwise
     */
    private function checkUsername($username)
    {
        $query = "SELECT * FROM aca_user WHERE username = :username";

        $db = $this->get('acadb');
        $result = $db->fetchRow($query, array('username', $username));

        return (bool)$result;
    }

    /**
     * Sets the login credentials in the user's session
     * @param string $username
     * @param string $password
     * @return Session Returns the user's session object.
     */
    private function setUserSession($username, $password)
    {
        $session = $this->getSession();

        $msg = null;

        if (!empty($username) && !empty($password)) {

            $query = "SELECT * FROM aca_user WHERE username = :username AND password = :password";

            $db = $this->get('acadb');
            $data = $db->fetchRow(
                $query,
                array('username' => $username, 'password' => $password)
            );

            if (empty($data)) {

                // set message
                $msg = 'Please check your credentials.';

                $session->set('loggedIn', false);
                $session->set('msg', $msg);

            } else {

                // get user information
                $name = $data['name'];
                $userID = $data['id'];

                // get cart information
                $cart = $this->get('cart');
                $cartID = $cart->getCartID($userID);

                // enter data into session
                $session->set('loggedIn', true);
                $session->set('username', $username);
                $session->set('userID', $userID);
                $session->set('cartID', $cartID);
                $session->set('name', $name);
                $session->set('msg', null);


            }
        }

        $session->save();

        return $session;
    }
}