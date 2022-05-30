<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotBlank;

class SimpleFormTextarea
{
    public static function renderField(FormEvent &$event): void
    {
        $constraints = [];

        if ($event->getData()->getRequired()) {
            $constraints[] = new NotBlank();
        }

        $event->getForm()->add($event->getData()->getSlug(), TextareaType::class, [
            'mapped'             => false,
            'label'              => $event->getData()->getLabel(),
            'help_html'          => true,
            'empty_data'         => null,
            'required'           => $event->getData()->getRequired(),
            'constraints'        => $constraints,
            'translation_domain' => false,
            'attr'               => [
                'rows'    => $event->getData()->getRows(),
            ],
        ]);
    }
}
