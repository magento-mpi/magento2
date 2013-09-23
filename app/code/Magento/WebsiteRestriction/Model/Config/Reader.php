<?php
/**
 * Website restrictions configuration filesystem loader. Loads configuration from XML files, split by scopes
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_WebsiteRestriction_Model_Config_Reader extends Magento_Config_Reader_Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/action' => 'path',
    );

    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_WebsiteRestriction_Model_Config_Converter $converter
     * @param Magento_WebsiteRestriction_Model_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_WebsiteRestriction_Model_Config_Converter $converter,
        Magento_WebsiteRestriction_Model_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'webrestrictions.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
