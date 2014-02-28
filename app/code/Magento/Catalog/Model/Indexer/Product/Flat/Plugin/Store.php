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
     * Product flat indexer processor
     *
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
     * @param \Magento\Core\Model\Resource\Store $subject
     * @param \Magento\Core\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(\Magento\Core\Model\Resource\Store $subject, \Magento\Core\Model\AbstractModel $object)
    {
        if (!$object->getId() || $object->dataHasChangedFor('group_id')) {
            $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        }
    }
}
