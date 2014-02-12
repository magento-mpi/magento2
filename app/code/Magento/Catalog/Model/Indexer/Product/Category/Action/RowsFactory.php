<?php
namespace Magento\Catalog\Model\Indexer\Product\Category\Action;

/**
 * Factory class for \Magento\Catalog\Model\Indexer\Product\Category\Action\Rows
 */
class RowsFactory extends \Magento\Catalog\Model\Indexer\Category\Product\Action\RowsFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\Catalog\Model\Indexer\Product\Category\Action\Rows'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
