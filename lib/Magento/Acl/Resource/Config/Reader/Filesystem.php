<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Acl\Resource\Config\Reader;

class Filesystem extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Config\ConverterInterface $converter
     * @param string $fileName
     * @param array $idAttributes
     * @param null|string $schema
     * @param null|string $perFileSchema
     * @param bool $isValidated
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        $fileName = 'acl.xml',
        $idAttributes = array(),
        $schema = null,
        $perFileSchema = null,
        $isValidated = true,
        $domDocumentClass = '\Magento\Acl\Resource\Config\Dom'
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
