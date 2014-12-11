<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogPermissions\Model\Indexer\Product\Action;

/**
 * Factory class for \Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows
 */
class RowsFactory extends \Magento\CatalogPermissions\Model\Indexer\Category\Action\RowsFactory
{
    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
