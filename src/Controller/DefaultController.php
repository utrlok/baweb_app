<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Security("is_granted('ROLE_USER')")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/dashboard", name="index")
     */
    public function indexAction(): Response
    {
        return $this->render('/index.html.twig');
    }
}