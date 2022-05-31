<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotBlank;

class SimpleFormInputField
{
    public static function renderField(FormEvent &$event): void
    {
        $class = 'TextType';
        $constraints = [];

        if ($event->getData()->getRequired()) {
            $constraints[] = new NotBlank();
        }

        $config = [
            'mapped'             => false,
            'label'              => $event->getData()->getLabel(),
            'help_html'          => true,
            'empty_data'         => null,
            'required'           => $event->getData()->getRequired(),
            'constraints'        => $constraints,
            'translation_domain' => false,
            'attr'               => [
                'autocomplete' => $event->getData()->getAutocomplete() ? 'on' : 'off',
                'placeholder'  => $event->getData()->getPlaceholder(),
                'type'         => $event->getData()->getInputType(),
                'value'        => $event->getData()->getDefaultValue(),
            ],
        ];

        switch ($event->getData()->getInputType()) {
            case 'hidden':
                $class = 'HiddenType';

                break;
            case 'date':
                $class = 'DateType';
                $config['widget'] = 'single_text';

                break;
            case 'time':
                $class = 'TimeType';

                break;
            case 'email':
                $class = 'EmailType';

                break;
            case 'number':
                $class = 'IntegerType';

                break;
            case 'password':
                $class = 'PasswordType';

                break;
            case 'tel':
                $class = 'TelType';

                break;
            case 'url':
                $class = 'UrlType';

                break;
        }

        $class = sprintf("Symfony\Component\Form\Extension\Core\Type\%s", $class);

        $event->getForm()->add($event->getData()->getSlug(), $class, $config);
    }
}
