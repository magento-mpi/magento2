<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Resource_Config_Reader_Filesystem extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\ConverterInterface $converter
     * @param Magento_Webapi_Model_Acl_Resource_Config_SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        Magento_Webapi_Model_Acl_Resource_Config_SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'acl.xml',
        $idAttributes = array(),
        $domDocumentClass = '\Magento\Acl\Resource\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

    /**
     * Read webapi resource list
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function read($scope)
    {
        return parent::read('webapi');
    }
}
