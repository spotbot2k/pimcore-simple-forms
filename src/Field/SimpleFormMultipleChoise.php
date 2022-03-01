<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SimpleFormMultipleChoise
{
    public static function renderQuestion(FormEvent &$event): void
    {
        $type = $event->getData()->getInputType();
        $options = [];
        foreach ($event->getData()->getOptions() as $option) {
            $options[$option['key']] = $option['value'];
        }

        $event->getForm()->add($event->getData()->getSlug(), ChoiceType::class, [
            'mapped'     => false,
            'label'      => $event->getData()->getLabel(),
            'help_html'  => true,
            'empty_data' => null,
            'required'   => $event->getData()->getRequired() ? 'on' : 'off',
            'choices'    => $options,
            'multiple'   => ($type === 'checkbox'),
            'expanded'   => ($type !== 'select'),
        ]);
    }
}
