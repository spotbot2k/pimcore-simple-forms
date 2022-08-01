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
use Symfony\Contracts\Translation\TranslatorInterface;

class SimpleFormService
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(SimpleForm $form, array $data): bool
    {
        if ($form->getUseHoneyPot() && !empty($data[SimpleFormType::HONEYPOT_FIELD_NAME])) {
            return false;
        }

        if ($form->getTimedSubmission()) {
            $timeTaken = time() - $data[SimpleFormType::INITS_FIELD_NAME];
            if ($timeTaken < $form->getTimedSubmission()) {
                return false;
            }
        }

        return true;
    }

    public function parseDataAsArray(array $formData, array $files = []): array
    {
        if (!array_key_exists('fields', $formData)) {
            return [];
        }

        $result = [];
        $stringValue = '';
        foreach ($formData['fields']['items'] as $field) {
            $result = array_merge($result, $field);
            $stringValue .= sprintf('%s: %s%s', $this->translator->trans("simple_form_{key($field)}"), reset($field), PHP_EOL);
        }

        foreach ($files as $key => $file) {
            if (!array_key_exists($key, $result)) {
                $result[$key] = $file;
            }
            $file = array_map(function ($item) {
                return basename($item);
            }, $file);
            $stringValue .= sprintf('%s: %s%s', $this->translator->trans("simple_form_{$key}"), implode(', ', $file), PHP_EOL);
        }

        $result['formValue'] = $stringValue;

        return $result;
    }

    public function handleUploads(SimpleForm $form, array $data): array
    {
        $uploadedFiles = [];

        foreach ($form->getFields() as $idx => $field) {
            if (array_key_exists($idx, $data['fields']['items']) && $field instanceof AbstractData) {
                $uploadedFiles[$field->getSlug()] = $this->uploadFile($field, $data['fields']['items'][$idx]);
            }
        }

        return $uploadedFiles;
    }

    private function uploadFile(AbstractData $field, $submittedData): array
    {
        if ($field->getType() !== 'SimpleFormFile' || is_null($submittedData)) {
            return [];
        }

        $uploadedFiles = [];

        foreach ($submittedData as $files) {
            if (is_null($files)) {
                continue;
            }

            if (is_array($files)) {
                foreach ($files as $file) {
                    $asset = $this->processFile($file, $field);
                    $uploadedFiles[$asset->getId()] = $asset;
                }
            } else {
                $asset = $this->processFile($files, $field);
                $uploadedFiles[$asset->getId()] = $asset;
            }
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
