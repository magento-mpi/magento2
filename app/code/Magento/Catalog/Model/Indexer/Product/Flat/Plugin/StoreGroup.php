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

class StoreGroup
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
        /** @var $storeGroup \Magento\Core\Model\Store\Group */
        $storeGroup = $methodArguments[0];
        if (!$storeGroup->getId() || $storeGroup->dataHasChangedFor('root_category_id')) {
            $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        }
        return $methodArguments;
    }
}
