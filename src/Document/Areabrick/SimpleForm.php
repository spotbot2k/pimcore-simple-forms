<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractTemplateAreabrick;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxInterface;
use Pimcore\Mail;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Page;
use Pimcore\Model\Document\Snippet;
use SimpleFormsBundle\Event\PostSendMailEvent;
use SimpleFormsBundle\Event\PreSendMailEvent;
use SimpleFormsBundle\Form\SimpleFormType;
use SimpleFormsBundle\Service\SimpleFormService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class SimpleForm extends AbstractTemplateAreabrick implements EditableDialogBoxInterface
{
    private FormFactoryInterface $formFactory;

    private SimpleFormService $service;

    private EventDispatcherInterface $dispatcher;

    private TranslatorInterface $translator;

    public function __construct(
        FormFactoryInterface $formFactory,
        SimpleFormService $service,
        EventDispatcherInterface $dispatcher,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->service = $service;
        $this->dispatcher = $dispatcher;
        $this->translator = $translator;
    }

    /**
     * Disables autodiscovery of the template - we need it to enable Pimcore 6.9 support
     */
    public function getTemplate()
    {
        return '@SimpleForms/areas/simple-form/view.html.twig';
    }

    public function getEditableDialogBoxConfiguration(Editable $area, ?Info $info): EditableDialogBoxConfiguration
    {
        $config = new EditableDialogBoxConfiguration();
        $config->setItems([
            'type'  => 'panel',
            'items' => [
                [
                    'type'   => 'relation',
                    'label'  => $this->translator->trans('pimcore_simple_forms.fe.selected_form_object'),
                    'name'   => 'formObject',
                    'config' => [
                        'types'    => ['object'],
                        'subtypes' => ['object'],
                        'classes'  => ['SimpleForm'],
                        'reload'   => true,
                        'width'    => 350,
                    ],
                ],
                [
                    'type'   => 'relation',
                    'label'  => $this->translator->trans('pimcore_simple_forms.fe.selected_form_success_redirection'),
                    'name'   => 'successRedirection',
                    'config' => [
                        'types'    => ['document'],
                        'subtypes' => ['page', 'snippet'],
                        'width'    => 350,
                    ],
                ],
            ],
        ]);

        return $config;
    }

    public function action(Info $info): ?Response
    {
        $formObject = $this->getDocumentEditable($info->getDocument(), 'relation', 'formObject')->getElement();
        $documentOnSuccess = $this->getDocumentEditable($info->getDocument(), 'relation', 'successRedirection')->getElement();
        $info->setParam('snippet', null);

        if (!is_null($formObject)) {
            $formBuilder = $this->formFactory->createBuilder(SimpleFormType::class, $formObject);

            if (!empty($formObject->getAction())) {
                $formBuilder->setAction($formObject->getAction()->getHref());
                $formBuilder->setMethod($formObject->getMethod());
            }

            $form = $formBuilder->getForm();
            $form->handleRequest($info->getRequest());

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->service->validate($formObject, $info->getRequest()->get(SimpleFormType::PREFIX))) {
                    $this->dispatcher->dispatch(new PreSendMailEvent($formObject, $info->getRequest()), PreSendMailEvent::NAME);
                    $files = [];

                    if (!empty($info->getRequest()->files->get(SimpleFormType::PREFIX))) {
                        $files = $this->service->handleUploads($formObject, $info->getRequest()->files->get(SimpleFormType::PREFIX));
                    }

                    $params = $this->service->parseDataAsArray($info->getRequest()->get(SimpleFormType::PREFIX), $files);

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

                    if (is_null($documentOnSuccess)) {
                        $documentOnSuccess = $formObject->getSuccessRedirect();
                    }

                    if ($documentOnSuccess instanceof Page) {
                        return new RedirectResponse($documentOnSuccess->getFullPath());
                    } elseif ($documentOnSuccess instanceof Snippet) {
                        $info->setParam('snippet', $documentOnSuccess);
                    }
                }
            }

            $info->setParam('form', $form->createView());
        } else {
            $info->setParam('form', null);
        }

        return null;
    }

    private function renderString(string $string, array $params): string
    {
        $twig = \Pimcore::getContainer()->get('twig');
        $template = $twig->createTemplate($string);

        return $template->render($params);
    }
}
