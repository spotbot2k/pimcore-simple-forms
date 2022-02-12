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
use Symfony\Component\Form\FormFactoryInterface;

class Form extends AbstractTemplateAreabrick
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function action(Info $info)
    {
        $formObject = $this->getDocumentEditable($info->getDocument(), 'relation', 'formObject')->getElement();
        if (!is_null($formObject)) {
            $form = $this->formFactory->createBuilder(SimpleFormType::class, $formObject)->getForm();
            $form->handleRequest($info->getRequest());

            $info->setParam('form', $form->createView());
        }
    }
}
