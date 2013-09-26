<?php
/**
 * Loads catalog attributes configuration from multiple XML files by merging them together
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Catalog\Model\Attribute\Config\Converter $converter
     * @param \Magento\Catalog\Model\Attribute\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Catalog\Model\Attribute\Config\Converter $converter,
        \Magento\Catalog\Model\Attribute\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState
    ) {
        $fileName = 'catalog_attributes.xml';
        $idAttributes = array(
            '/config/group' => 'name',
            '/config/group/attribute' => 'name',
        );
        parent::__construct($fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes);
    }
}
