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
     * Product flat indexer processor
     *
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_productFlatIndexerProcessor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
     */
    public function __construct(\Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor)
    {
        $this->_productFlatIndexerProcessor = $productFlatIndexerProcessor;
    }

    /**
     * Before save handler
     *
     * @param \Magento\Core\Model\Resource\Store\Group $subject
     * @param \Magento\Model\AbstractModel $object
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        \Magento\Core\Model\Resource\Store\Group $subject,
        \Magento\Model\AbstractModel $object
    ) {
        if (!$object->getId() || $object->dataHasChangedFor('root_category_id')) {
            $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
        }
    }
}
