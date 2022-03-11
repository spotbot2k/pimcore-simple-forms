<?php

namespace SimpleFormsBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Pimcore\Model\DataObject\SimpleForm;

class PreSendMailEvent extends Event
{
    public const NAME = 'simple_forms.pre_send_mail';

    protected SimpleForm $form;

    public function __construct(SimpleForm $form)
    {
        $this->form = $form;
    }

    public function getFormObject()
    {
        return $this->form;
    }
}
