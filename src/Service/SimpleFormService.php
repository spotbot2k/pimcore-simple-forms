<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Service;

use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\SimpleForm;
use SimpleFormsBundle\Form\SimpleFormType;

class SimpleFormService
{
    public function validate(SimpleForm $form, array $data): bool
    {
        if ($form->getUseHoneyPot() && !empty($data[SimpleFormType::HONEYPOT_FIELD_NAME])) {
            return false;
        }

        foreach ($form->getFields() as $idx => $field) {
            if (!$this->validateField($field, $data['fields']['items'][$idx])) {
                return false;
            }
        }

        return true;
    }

    private function validateField(AbstractData $field, $submitedData): bool
    {
        if ($field->getRequired()) {
            return !empty($submitedData[$field->getSlug()]);
        }

        return true;
    }
}
