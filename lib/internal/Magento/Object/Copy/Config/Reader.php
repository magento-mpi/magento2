<?php
/**
 * Fieldset configuration reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Object\Copy\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of identifier attributes for merging
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/scope' => 'id',
        '/config/scope/fieldset' => 'id',
        '/config/scope/fieldset/field' => 'name',
        '/config/scope/fieldset/field/aspect' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Object\Copy\Config\Converter $converter
     * @param \Magento\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Object\Copy\Config\Converter $converter,
        \Magento\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'fieldset.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom',
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
