<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Field;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class SimpleFormFile
{
    public static function renderField(FormEvent &$event): void
    {
        $maxSizeMb = intval($event->getData()->getMaxSize()) * 1024 * 1024;

        if ($event->getData()->getMultiple()) {
            $constraints = [new All(['constraints' => new File([], $maxSizeMb)])];
        } else {
            $constraints = new File([], $maxSizeMb);
        }

        $event->getForm()->add($event->getData()->getSlug(), FileType::class, [
            'mapped'             => false,
            'label'              => $event->getData()->getLabel(),
            'help_html'          => true,
            'required'           => $event->getData()->getRequired(),
            'multiple'           => $event->getData()->getMultiple(),
            'constraints'        => $constraints,
            'translation_domain' => false,
            'attr'               => [
                'accept' => $event->getData()->getAccept(),
                'size'   => $maxSizeMb,
            ],
        ]);
    }
}
