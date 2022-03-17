<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Event;

use Pimcore\Model\DataObject\SimpleForm;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PreSendMailEvent extends Event
{
    public const NAME = 'simple_forms.pre_send_mail';

    protected SimpleForm $form;

    protected Request $request;

    public function __construct(SimpleForm $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    public function getFormObject()
    {
        return $this->form;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
