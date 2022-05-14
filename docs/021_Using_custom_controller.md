# Using Custom Controller

If a custom action is given the form will add a hidden field named `form_referer` so you can trace back the original object afterwards.

In the controller you can use the submitted data (and files) or validate them first.

``` php
<?php

namespace App\Controller;

use Pimcore\Model\DataObject\SimpleForm;
use SimpleFormsBundle\Form\SimpleFormType;
use SimpleFormsBundle\Service\SimpleFormService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends BaseController
{
    /**
     * @Route("/form/hello", name="form_examples_hello")
     *
     * @param Request $request
     * @return Response
     */
    public function welcomeAction(Request $request, FormFactoryInterface $formFactory, SimpleFormService $service)
    {
        if ($formId = $request->get('simple_form')['form_referer']) {
            $formObject = SimpleForm::getById($formId);
            $form = $formFactory->createBuilder(SimpleFormType::class, $formObject)->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $service->parseDataAsArray($request->get(SimpleFormType::PREFIX));

                return new Response("Hello {$data['name']}!");
            }
        }
        

        return new Response();
    }
}
```