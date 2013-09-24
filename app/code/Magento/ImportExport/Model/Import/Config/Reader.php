<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Import_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/entity' => 'name',
        '/config/productType' => 'name',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_ImportExport_Model_Import_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_ImportExport_Model_Import_Config_SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_ImportExport_Model_Import_Config_Converter $converter,
        Magento_ImportExport_Model_Import_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'import.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
