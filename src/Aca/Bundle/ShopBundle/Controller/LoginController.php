<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Aca\Bundle\ShopBundle\Db\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    public function loginFormAction(Request $request)
    {
        $session = $this->getSession();

        $msg = null;

        $username = $request->get('username');
        $password = $request->get('password');


        if (!empty($username) && !empty($password)) {

            $query = "SELECT * FROM aca_user WHERE username='$username' AND password='$password'";

            $db = new Database();
            $data = $db->fetchRowMany($query);

            if (empty($data) && $request->getMethod() == 'POST') {

                $msg = 'Please check your credentials.';
                $session->set('loggedIn', false);

            } else {

                $row = array_pop($data);
                $name = $row['name'];

                $session->set('loggedIn', true);
                $session->set('name', $name);

            }
        }

        $session->save();

        $loggedIn = $session->get('loggedIn');
        $name = $session->get('name');


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
        $session->remove('name');
        $session->save();

        return new RedirectResponse('/login');
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
}