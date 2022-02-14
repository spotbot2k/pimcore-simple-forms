<?php

namespace SimpleFormsBundle\Tools;

use DirectoryIterator;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;
use Pimcore\Logger;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Fieldcollection;

class Installer extends SettingsStoreAwareInstaller
{
    /**
     * {@inheritdoc}
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function install(): bool
    {
        $this->installFieldCollections();
        $this->installClasses();

        parent::install();

        return true;
    }

    /**
     * Scan resources for serialized class files and import them if found
     * The name of the class wil lbe extracted from the filename of the dump
     *
     * @return void
     */
    private function installClasses(): void
    {
        $sourcePath = __DIR__.'/../Resources/install/class_sources';

        foreach (new DirectoryIterator($sourcePath) as $fileInfo) {
            if ($fileInfo->getExtension() === 'json') {
                $this->installClass($this->getObjectNameFromExport($fileInfo->getBasename()), sprintf('%s/%s', $sourcePath, $fileInfo->getFilename()));
            }
        }
    }

    private function installClass($classname, $filepath): void
    {
        $class = ClassDefinition::getByName($classname);
        if (!$class) {
            $class = new ClassDefinition();
            $class->setName($classname);
            $class->setGroup('SimpleForms');

            $success = Service::importClassDefinitionFromJson($class, file_get_contents($filepath));
            if (!$success) {
                Logger::err("Could not import $classname Class.");
            }
        }
    }

    public function installFieldCollections()
    {
        $sourcePath = __DIR__.'/../Resources/install/field_collections';

        foreach (new DirectoryIterator($sourcePath) as $fileInfo) {
            if ($fileInfo->getExtension() === 'json') {
                $this->installFieldCollection(
                    $this->getObjectNameFromExport($fileInfo->getBasename(), 'fieldcollection_'),
                    sprintf('%s/%s', $sourcePath, $fileInfo->getFilename()),
                );
            }
        }
    }

    public function installFieldCollection($key, $filepath)
    {
        $fieldCollection = Fieldcollection\Definition::getByKey($key);
        if (!$fieldCollection) {
            $fieldCollection = new Fieldcollection\Definition();
            $fieldCollection->setKey($key);
            $success = Service::importFieldCollectionFromJson($fieldCollection, file_get_contents($filepath));

            if (!$success) {
                throw new InstallationException(sprintf('Failed to import field collection "%s"', $key));
            }
        }
    }

    private function getObjectNameFromExport(string $filename, string $type = 'class_'): string
    {
        return str_replace($type, '', str_replace('_export.json', '', $filename));
    }
}
