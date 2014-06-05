<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock\Action;

/**
 * Class Rows reindex action for mass actions
 *
 * @package Magento\CatalogInventory\Model\Indexer\Stock\Action
 */
class Rows extends \Magento\CatalogInventory\Model\Indexer\Stock\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @throws \Magento\CatalogInventory\Exception
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\CatalogInventory\Exception(__('Bad value was supplied.'));
        }
        $this->_logger->log('Rows reindex for products - ' . implode(",", $ids) . '');
    }
}
