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

class Store
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
     * Before save handler
     *
     * @param array $methodArguments
     * @return array
     */
    public function beforeSave(array $methodArguments)
    {
        /** @var $store \Magento\Core\Model\Store */
        $store = $methodArguments[0];
        if (!$store->getId() || $store->dataHasChangedFor('group_id')) {
            $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        }
        return $methodArguments;
    }
}
