<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_Resource_Config_Reader_Filesystem extends Magento_Config_Reader_Filesystem
{
    /**
     * @param Magento_Config_FileResolverInterface $fileResolver
     * @param Magento_Config_ConverterInterface $converter
     * @param Magento_Acl_Resource_Config_SchemaLocator $schemaLocator
     * @param Magento_Config_ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Config_ConverterInterface $converter,
        Magento_Acl_Resource_Config_SchemaLocator $schemaLocator,
        Magento_Config_ValidationStateInterface $validationState,
        $fileName = 'acl.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento_Acl_Resource_Config_Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
