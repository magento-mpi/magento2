<?php
/**
 * Indexer configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/indexer' => 'name',
        '/config/indexer/depends' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Index_Model_Indexer_Config_Converter $converter
     * @param Magento_Index_Model_Indexer_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Index_Model_Indexer_Config_Converter $converter,
        Magento_Index_Model_Indexer_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'indexers.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
