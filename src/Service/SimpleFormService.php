<?php

/**
 * This file is part of the Pimcore Simple Forms Bundle
 *
 *  @license GPLv3
 */

namespace SimpleFormsBundle\Service;

use Pimcore\Model\Asset;
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
            if (array_key_exists($idx, $data['fields']['items']) && !$this->validateField($field, $data['fields']['items'][$idx])) {
                return false;
            }
        }

        return true;
    }

    public function handleUploads(SimpleForm $form, array $data): array
    {
        $uploadedFiles = [];

        foreach ($form->getFields() as $idx => $field) {
            if (array_key_exists($idx, $data['fields']['items'])) {
                $uploadedFiles[$field->getSlug()] = $this->uploadFile($field, $data['fields']['items'][$idx]);
            }
        }

        return $uploadedFiles;
    }

    private function uploadFile(AbstractData $field, $submitedData): array
    {
        if ($field->getType() !== 'SimpleFormFile' || is_null($submitedData)) {
            return [];
        }

        $uploadedFiles = [];

        foreach ($submitedData as $file) {
            $parent = $field->getUploadPath() ?: Asset::getById(1);

            if (is_null($file)) {
                continue;
            }

            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            if ($field->getRandomizeName()) {
                $name = uniqid();
            }

            $asset = new Asset();
            $asset->setFilename(sprintf('%s.%s', $name, $file->guessExtension()));
            $asset->setData(file_get_contents($file->getPathname()));
            $asset->setParent($parent);
            $asset->save();

            $uploadedFiles[$asset->getId()] = $asset;
        }

        return $uploadedFiles;
    }

    private function validateField(AbstractData $field, $submitedData): bool
    {
        if ($field->getRequired()) {
            return !empty($submitedData[$field->getSlug()]);
        }

        return true;
    }
}
