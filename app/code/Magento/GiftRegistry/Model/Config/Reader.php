<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftRegistry configuration filesystem loader. Loads gift registry configuration from XML file
 */
class Magento_GiftRegistry_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/attribute_type' => 'name',
        '/config/attribute_group' => 'name',
        '/config/registry/static_attribute' => 'name',
        '/config/registry/custom_attribute' => 'name',
        '/config/registrant/static_attribute' => 'name',
        '/config/registrant/custom_attribute' => 'name'
    );

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
