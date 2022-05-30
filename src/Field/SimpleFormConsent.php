<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;

class SimpleFormConsent
{
    public static function renderField(FormEvent &$event): void
    {
        $event->getForm()->add($event->getData()->getSlug(), CheckboxType::class, [
            'mapped'             => false,
            'label'              => $event->getData()->getLabel(),
            'label_attr'         => [ 'style' => 'display: none' ],
            'help'               => $event->getData()->getHelp(),
            'help_html'          => true,
            'translation_domain' => false,
            'required'           => $event->getData()->getRequired(),
        ]);
    }
}
