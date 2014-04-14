<?php
/**
 * Customer address format configuration filesystem loader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Address\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            'address_formats.xml',
            array('/config/format' => 'code')
        );
    }
}
