<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu configuration files handler
 */
class Mage_Backend_Model_Menu_Config_Reader extends Magento_Config_Reader_Filesystem
{
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Config_FileResolverInterface $fileResolver,
        Mage_Backend_Model_Menu_Config_Converter $converter,
        $fileName = 'menu.xml',
        $idAttributes = array(),
        $schema = null,
        $perFileSchema = null,
        $isValidated = false,
        $domDocumentClass = 'Mage_Backend_Model_Menu_Config_Menu_Dom'
    ) {
        $schema = $moduleReader->getModuleDir('etc', 'Mage_Backend') . DIRECTORY_SEPARATOR . 'menu.xsd';
        parent::__construct(
            $fileResolver,
            $converter,
            $fileName,
            $idAttributes,
            $schema,
            $perFileSchema,
            $isValidated,
            $domDocumentClass
        );
    }
}
