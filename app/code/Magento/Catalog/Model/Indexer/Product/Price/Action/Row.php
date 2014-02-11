<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

/**
 * Class Row reindex action
 * @package Magento\Catalog\Model\Indexer\Product\Price\Action
 */
class Row extends \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $id
     * @throws \Magento\Core\Exception
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Core\Exception(__('Could not rebuild index for undefined product'));
        }
        foreach ($this->_storeManager->getStores() as $store) {
            $this->_logger->log('Row reindex for store - ' . $store->getId() . ' and product - ' . $id . '');
        }
    }
}
