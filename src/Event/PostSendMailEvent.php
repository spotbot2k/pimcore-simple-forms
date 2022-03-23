<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Event;

use Pimcore\Model\DataObject\SimpleForm;
use Symfony\Contracts\EventDispatcher\Event;

class PostSendMailEvent extends Event
{
    public const NAME = 'simple_forms.post_send_mail';

    protected SimpleForm $form;

    protected array $data;

    public function __construct(SimpleForm $form, array $data)
    {
        $this->form = $form;
        $this->data = $data;
    }

    public function getFormObject()
    {
        return $this->form;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
