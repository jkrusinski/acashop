<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function homepageAction(){
        return $this->render(
            'AcaShopBundle:Default:index.html.twig'
        );
    }

}
