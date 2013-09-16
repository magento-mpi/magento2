<?php
/**
 * Customer address format configuration filesystem loader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Customer_Model_Address_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Customer_Model_Address_Config_Converter $converter
     * @param Magento_Customer_Model_Address_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Customer_Model_Address_Config_Converter $converter,
        Magento_Customer_Model_Address_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, 'address_formats.xml', array(
                '/config/format' => 'code'
        ));
    }
}
