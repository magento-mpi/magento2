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
 * Class Rows reindex action for mass actions
 *
 * @package Magento\Catalog\Model\Indexer\Product\Price\Action
 */
class Rows extends \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
{
    /**
     * @param array $ids
     * @return \Magento\Catalog\Model\Indexer\Product\Price\Action\Rows|void
     * @throws \Magento\Core\Exception
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Core\Exception(__('Bad value was supplied.'));
        }
        foreach ($this->_storeManager->getStores() as $store) {
            $this->_logger->log('Rows reindex for store - ' . $store->getId() . ' and products - ' . implode(",", $ids) . '');
        }
        return $this;
    }
}
