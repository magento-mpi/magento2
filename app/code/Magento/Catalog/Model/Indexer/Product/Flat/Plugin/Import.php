<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat\Plugin;

class Import
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_productFlatIndexerProcessor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
    ) {
        $this->_productFlatIndexerProcessor = $productFlatIndexerProcessor;
    }

    /**
     * After improt handler
     *
     * @param Object $import
     * @return mixed
     */
    public function afterImportSource($import)
    {
        $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        return $import;
    }
}
