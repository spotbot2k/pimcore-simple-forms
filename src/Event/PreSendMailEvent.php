<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Event;

use Pimcore\Model\DataObject\SimpleForm;
use Symfony\Contracts\EventDispatcher\Event;

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
