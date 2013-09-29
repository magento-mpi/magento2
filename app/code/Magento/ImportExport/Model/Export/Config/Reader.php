<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Export\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/entity' => 'name',
        '/config/productType' => 'name',
        '/config/fileFormat' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\ImportExport\Model\Export\Config\Converter $converter
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param \Magento\ImportExport\Model\Export\Config\SchemaLocator $schemaLocator
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\ImportExport\Model\Export\Config\Converter $converter,
        \Magento\ImportExport\Model\Export\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'export.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }

}
