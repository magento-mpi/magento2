<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @param \Magento\ObjectManager $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        $instanceName = 'Magento\CatalogPermissions\Model\Indexer\Product\Action\Rows'
    ) {
        parent::__construct($objectManager, $instanceName);
    }
}
