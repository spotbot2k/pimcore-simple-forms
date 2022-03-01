<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SimpleFormTextarea
{
    public static function renderQuestion(FormEvent &$event): void
    {
        $event->getForm()->add($event->getData()->getSlug(), TextareaType::class, [
            'mapped'     => false,
            'label'      => $event->getData()->getLabel(),
            'help_html'  => true,
            'empty_data' => null,
            'required'   => $event->getData()->getRequired() ? 'on' : 'off',
            "attr"       => [
                "rows"   => $event->getData()->getRows(),
            ]
        ]);
    }
}
