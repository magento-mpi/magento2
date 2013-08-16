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
     * @param string $fileName
     * @param array $idAttributes
     * @param null|string $schema
     * @param null|string $perFileSchema
     * @param bool $isValidated
     * @param string $domDocumentClass
     */
    public function __construct(
        Magento_Config_FileResolverInterface $fileResolver,
        Magento_Config_ConverterInterface $converter,
        $fileName = 'acl.xml',
        $idAttributes = array(),
        $schema = null,
        $perFileSchema = null,
        $isValidated = true,
        $domDocumentClass = 'Magento_Acl_Resource_Config_Dom'
    ) {
        $schema = realpath(__DIR__ . '/../../../etc/acl.xsd');
        parent::__construct(
            $fileResolver,
            $converter,
            $fileName,
            $idAttributes,
            $schema,
            $perFileSchema,
            $isValidated,
            $domDocumentClass
        );
    }
}
