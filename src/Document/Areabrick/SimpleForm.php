<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Mail;
use Pimcore\Model\Document\Editable\Area\Info;
use SimpleFormsBundle\Event\PreSendMailEvent;
use SimpleFormsBundle\Event\PostSendMailEvent;
use SimpleFormsBundle\Form\SimpleFormType;
use SimpleFormsBundle\Service\SimpleFormService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SimpleForm extends AbstractTemplateAreabrick
{
    private FormFactoryInterface $formFactory;

    private SimpleFormService $service;

    private EventDispatcherInterface $dispatcher;

    public function __construct(FormFactoryInterface $formFactory, SimpleFormService $service, EventDispatcherInterface $dispatcher)
    {
        $this->formFactory = $formFactory;
        $this->service = $service;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Disables autodiscovery of the template - we need it to enable Pimcore 6.9 support
     */
    public function getTemplate()
    {
        return '@SimpleForms/areas/simple-form/view.html.twig';
    }

    public function action(Info $info): ?Response
    {
        $formObject = $this->getDocumentEditable($info->getDocument(), 'relation', 'formObject')->getElement();
        if (!is_null($formObject)) {
            $form = $this->formFactory->createBuilder(SimpleFormType::class, $formObject)->getForm();
            $form->handleRequest($info->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->service->validate($formObject, $info->getRequest()->get(SimpleFormType::PREFIX))) {
                    $this->dispatcher->dispatch(new PreSendMailEvent($formObject, $info->getRequest()), PreSendMailEvent::NAME);
                    $files = [];

                    if (!empty($info->getRequest()->files->get(SimpleFormType::PREFIX))) {
                        $files = $this->service->handleUploads($formObject, $info->getRequest()->files->get(SimpleFormType::PREFIX));
                    }

                    $params = $this->parseDataForMail($info->getRequest()->get(SimpleFormType::PREFIX), $files);

                    foreach ($formObject->getEmailDocuments() as $mailDocument) {
                        $mailDocument->setTo($this->renderString($mailDocument->getTo(), $params));
                        $mailDocument->setCc($this->renderString($mailDocument->getCc(), $params));
                        $mailDocument->setBcc($this->renderString($mailDocument->getBcc(), $params));

                        $mail = new Mail();
                        $mail->setDocument($mailDocument);
                        $mail->setParams($params);
                        $mail->send();
                    }

                    $this->dispatcher->dispatch(new PostSendMailEvent($formObject, $params), PostSendMailEvent::NAME);

                    if (!empty($formObject->getSuccessRedirect())) {
                        return new RedirectResponse($formObject->getSuccessRedirect()->getFullPath());
                    }
                }
            }

            $info->setParam('form', $form->createView());
        } else {
            $info->setParam('form', null);
        }

        return null;
    }

    private function parseDataForMail(array $formData, array $files = []): array
    {
        $result = [];

        foreach ($formData['fields']['items'] as $idx => $field) {
            $result = array_merge($result, $field);
        }

        foreach ($files as $key => $file) {
            if (!array_key_exists($key, $result)) {
                $result[$key] = $file;
            }
        }

        return $result;
    }

    private function renderString(string $string, array $params): string
    {
        $twig = \Pimcore::getContainer()->get('twig');
        $template = $twig->createTemplate($string);

        return $template->render($params);
    }
}
