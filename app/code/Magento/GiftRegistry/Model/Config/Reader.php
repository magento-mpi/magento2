<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * GiftRegistry configuration filesystem loader. Loads gift registry configuration from XML file
 *
 * Class Magento_GiftRegistry_Model_Config_Reader
 */
class Magento_GiftRegistry_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_GiftRegistry_Model_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_GiftRegistry_Model_Config_SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_GiftRegistry_Model_Config_Converter $converter,
        Magento_GiftRegistry_Model_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'giftregistry.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
