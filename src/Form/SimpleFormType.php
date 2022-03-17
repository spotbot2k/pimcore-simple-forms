<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Form;

use Pimcore\Model\DataObject\SimpleForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleFormType extends AbstractType
{
    public const PREFIX = 'simple_form';

    public const HONEYPOT_FIELD_NAME = 'email';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $event->getForm()->add('fields', SimpleFormFieldCollectionType::class, [ 'label' => false ]);
            if ($event->getData()->getUseHoneyPot()) {
                $event->getForm()->add(self::HONEYPOT_FIELD_NAME, TextType::class, [ 'required' => false, 'mapped' => false ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $event->getForm()->add('submit', SubmitType::class);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SimpleForm::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::PREFIX;
    }
}
