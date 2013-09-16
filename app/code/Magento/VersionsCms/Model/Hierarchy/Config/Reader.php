<?php
/**
 * Cms menu hierarchy config reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Hierarchy_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/menuLayout' => 'name',
        '/config/menuLayout/pageLayout' => 'handle',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_VersionsCms_Model_Hierarchy_Config_Converter $converter
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_VersionsCms_Model_Hierarchy_Config_Converter $converter,
        Magento_VersionsCms_Model_Hierarchy_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'menuHierarchy.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
