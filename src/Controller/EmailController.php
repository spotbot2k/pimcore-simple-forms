<?php

namespace SimpleFormsBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends FrontendController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function mailAction(Request $request)
    {
        return $this->render('@SimpleForms/layouts/email.html.twig');
    }
}