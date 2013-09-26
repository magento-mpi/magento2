<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes\Config;

class Reader extends \Magento\Config\Reader\Filesystem
{
    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = array(
        '/config/type' => 'name',
        '/config/type/priceModel' => 'instance',
        '/config/type/indexerModel' => 'instance',
        '/config/type/stockIndexerModel' => 'instance',
        '/config/type/allowProductTypes/type' => 'name',
        '/config/type/allowedSelectionTypes/type' => 'name',
    );

    /**
     * @param \Magento\Config\FileResolverInterface $fileResolver
     * @param \Magento\Catalog\Model\ProductTypes\Config\Converter $converter
     * @param \Magento\Catalog\Model\ProductTypes\Config\SchemaLocator $schemaLocator
     * @param \Magento\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     */
    public function __construct(
        \Magento\Config\FileResolverInterface $fileResolver,
        \Magento\Catalog\Model\ProductTypes\Config\Converter $converter,
        \Magento\Catalog\Model\ProductTypes\Config\SchemaLocator $schemaLocator,
        \Magento\Config\ValidationStateInterface $validationState,
        $fileName = 'product_types.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Config\Dom'
    ) {
        parent::__construct(
            $fileResolver, $converter, $schemaLocator, $validationState, $fileName, $idAttributes, $domDocumentClass
        );
    }
}
