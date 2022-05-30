<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;

class SimpleFormMultipleChoise
{
    public static function renderField(FormEvent &$event): void
    {
        $type = $event->getData()->getInputType();
        $options = [];
        foreach ($event->getData()->getOptions() as $option) {
            $options[$option['value']] = $option['key'];
        }

        $event->getForm()->add($event->getData()->getSlug(), ChoiceType::class, [
            'mapped'             => false,
            'label'              => $event->getData()->getLabel(),
            'help_html'          => true,
            'empty_data'         => null,
            'required'           => $event->getData()->getRequired() || ($type === 'radio'),
            'choices'            => $options,
            'multiple'           => ($type === 'checkbox'),
            'expanded'           => ($type !== 'select'),
            'data'               => ($type !== 'checkbox') ? $event->getData()->getDefaultValue() : [],
            'translation_domain' => false,
        ]);
    }
}
