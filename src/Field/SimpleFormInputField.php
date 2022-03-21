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

        switch ($event->getData()->getInputType()) {
            case 'hidden':
                $class = 'HiddenType';

                break;
            case 'date':
                $class = 'DateType';

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
        $constraints = [];

        if ($event->getData()->getRequired()) {
            $constraints[] = new NotBlank();
        }

        $event->getForm()->add($event->getData()->getSlug(), $class, [
            'mapped'      => false,
            'label'       => $event->getData()->getLabel(),
            'help_html'   => true,
            'empty_data'  => null,
            'required'    => $event->getData()->getRequired(),
            'constraints' => $constraints,
            'attr'        => [
                'autocomplete' => $event->getData()->getAutocomplete() ? 'on' : 'off',
                'placeholder'  => $event->getData()->getPlaceholder(),
                'type'         => $event->getData()->getInputType(),
            ],
        ]);
    }
}
