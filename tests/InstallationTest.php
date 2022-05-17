<?php

class InstallationTest extends \Codeception\Test\Unit
{
    public function testInstallation()
    {
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\SimpleForm'));
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\Fieldcollection\Data\SimpleFormFile'));
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\Fieldcollection\Data\SimpleFormConsent'));
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\Fieldcollection\Data\SimpleFormInputField'));
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\Fieldcollection\Data\SimpleFormMultipleChoise'));
        $this->assertTrue(class_exists('\Pimcore\Model\DataObject\Fieldcollection\Data\SimpleFormTextarea'));
    }
}
