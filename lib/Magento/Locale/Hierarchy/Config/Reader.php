<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Locale\Hierarchy\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/locale' => 'code',
    );

    /**
     * @param FileResolver $fileResolver
     * @param Converter $converter
     * @param SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\Locale\Hierarchy\Config\FileResolver $fileResolver,
        \Magento\Locale\Hierarchy\Config\Converter $converter,
        \Magento\Locale\Hierarchy\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'config.xml',
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
