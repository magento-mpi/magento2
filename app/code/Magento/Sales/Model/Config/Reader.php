<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales configuration filesystem loader. Loads all totals (incl. nominal, creditmemo, invoice)
 * configuration from XML file
 */
class Magento_Sales_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/section' => 'name',
        '/config/section/group' => 'name',
        '/config/section/group/item' => 'name',
        '/config/order/available_product_type' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Sales_Model_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_Sales_Model_Config_SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Sales_Model_Config_Converter $converter,
        Magento_Sales_Model_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'sales.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
