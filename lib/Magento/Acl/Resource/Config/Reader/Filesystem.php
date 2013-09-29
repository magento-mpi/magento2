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
     * @param \Magento\Acl\Resource\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Config\ConverterInterface $converter,
        \Magento\Acl\Resource\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'acl.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Acl\Resource\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
