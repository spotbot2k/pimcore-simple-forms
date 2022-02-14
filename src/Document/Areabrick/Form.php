<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use SimpleFormsBundle\Form\SimpleFormType;
use SimpleFormsBundle\Service\SimpleFormService;
use Symfony\Component\Form\FormFactoryInterface;

class Form extends AbstractTemplateAreabrick
{
    private FormFactoryInterface $formFactory;

    private SimpleFormService $service;

    public function __construct(FormFactoryInterface $formFactory, SimpleFormService $service)
    {
        $this->formFactory = $formFactory;
        $this->service = $service;
    }

    public function action(Info $info)
    {
        $formObject = $this->getDocumentEditable($info->getDocument(), 'relation', 'formObject')->getElement();
        if (!is_null($formObject)) {
            $form = $this->formFactory->createBuilder(SimpleFormType::class, $formObject)->getForm();
            $form->handleRequest($info->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                $isValid = $this->service->validate($formObject, $info->getRequest()->get(SimpleFormType::PREFIX));
            }

            $info->setParam('form', $form->createView());
        }
    }
}
