<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_ProductTypes_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/type' => 'name',
        '/config/type/priceModel' => 'instance',
        '/config/type/indexerModel' => 'instance',
        '/config/type/stockIndexerModel' => 'instance',
        '/config/type/allowProductTypes/type' => 'name',
        '/config/type/allowedSelectionTypes/type' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Catalog_Model_ProductTypes_Config_Converter $converter
     * @param Magento_Catalog_Model_ProductTypes_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Catalog_Model_ProductTypes_Config_Converter $converter,
        Magento_Catalog_Model_ProductTypes_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'product_types.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
