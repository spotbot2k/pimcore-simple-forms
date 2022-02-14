<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\FormEvent;

class SimpleFormInputField
{
    public static function renderQuestion(FormEvent &$event): void
    {
        $class = 'TextType';

        switch ($event->getData()->getInputType()) {
            case 'hidden':
                $class = 'HiddenType';
        }

        $class = sprintf("Symfony\Component\Form\Extension\Core\Type\%s", $class);

        $event->getForm()->add($event->getData()->getSlug(), $class, [
            'mapped'     => false,
            'label'      => $event->getData()->getLabel(),
            'help_html'  => true,
            'empty_data' => null,
            'required'   => $event->getData()->getRequired() ? 'on' : 'off',
            'attr'       => [
                'autocomplete' => $event->getData()->getAutocomplete() ? 'on' : 'off',
                'placeholder' => $event->getData()->getPlaceholder(),
            ],
        ]);
    }
}
