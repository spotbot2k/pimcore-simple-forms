<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Mail;
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
                if ($this->service->validate($formObject, $info->getRequest()->get(SimpleFormType::PREFIX))) {
                    foreach ($formObject->getEmailDocuments() as $mailDocument) {
                        $mail = new Mail();
                        $mail->setDocument($mailDocument);
                        $mail->setParams($this->parseDataForMail($info->getRequest()->get(SimpleFormType::PREFIX)));
                        // @todo: replace params in to, cc and bcc
                        $mail->send();
                    }
                }
            }

            $info->setParam('form', $form->createView());
        }
    }

    private function parseDataForMail(array $formData): array
    {
        $result = [];

        foreach ($formData["fields"]["items"] as $idx => $field) {
            $result = array_merge($result, $field);
        }

        return $result;
    }
}
