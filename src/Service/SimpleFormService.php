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
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SimpleFormService
{
    public function validate(SimpleForm $form, array $data): bool
    {
        if ($form->getUseHoneyPot() && !empty($data[SimpleFormType::HONEYPOT_FIELD_NAME])) {
            return false;
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

        foreach ($submitedData as $files) {
            if (is_null($files)) {
                continue;
            }

            if (is_array($files)) {
                foreach ($files as $file) {
                    $asset = $this-> processFile($file, $field);
                    $uploadedFiles[$asset->getId()] = $asset;
                }
            } else {
                $asset = $this-> processFile($files, $field);
                $uploadedFiles[$asset->getId()] = $asset;
            }

            $uploadedFiles[$asset->getId()] = $asset;
        }

        return $uploadedFiles;
    }

    private function processFile(UploadedFile $file, AbstractData $field): Asset
    {
        $parent = $field->getUploadPath() ?: Asset::getById(1);
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if ($field->getRandomizeName()) {
            $name = uniqid();
        }

        $asset = new Asset();
        $asset->setFilename(sprintf('%s.%s', $name, $file->guessExtension()));
        $asset->setData(file_get_contents($file->getPathname()));
        $asset->setParent($parent);
        $asset->save();

        return $asset;
    }
}
