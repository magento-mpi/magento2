<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Action;

/**
 * Class Full reindex action
 *
 * @package Magento\CatalogInventory\Model\Indexer\Stock\Action
 */
class Full extends \Magento\CatalogInventory\Model\Indexer\Stock\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param null|array $ids
     * @throws \Magento\CatalogInventory\Exception
     */
    public function execute($ids = null)
    {
        try {
            $this->_logger->log('Full reindex');
            $this->reindexAll();
        } catch (\Exception $e) {
            throw new \Magento\CatalogInventory\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
