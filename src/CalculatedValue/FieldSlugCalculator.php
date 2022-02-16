<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\CalculatedValue;

use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Model\DataObject\SimpleForm;

class FieldSlugCalculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        if ($object instanceof SimpleForm) {
            $string = str_replace(' ', '_', $object->getFields()->get($context->getIndex())->getLabel());
            $string = str_replace('-', '_', $string);

            return strtolower($string);
        }

        return '';
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
        return $this->compute($object, $context);
    }
}
