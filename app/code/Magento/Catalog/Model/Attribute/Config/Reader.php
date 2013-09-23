<?php
/**
 * Loads catalog attributes configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Catalog_Model_Attribute_Config_Converter $converter
     * @param Magento_Catalog_Model_Attribute_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Catalog_Model_Attribute_Config_Converter $converter,
        Magento_Catalog_Model_Attribute_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState
    ) {
        $fileName = 'catalog_attributes.xml';
        $idAttributes = array(
            '/config/group' => 'name',
            '/config/group/attribute' => 'name',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
    }
}
