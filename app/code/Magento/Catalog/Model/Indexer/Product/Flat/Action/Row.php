<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat\Action;

/**
 * Class Row reindex action
 * @package Magento\Catalog\Model\Indexer\Product\Flat\Action
 */
class Row extends \Magento\Catalog\Model\Indexer\Product\Flat\AbstractAction
{
    /**
     * Execute row reindex action
     *
     * @param int|null $id
     * @return \Magento\Catalog\Model\Indexer\Product\Flat\Action\Row
     * @throws \Magento\Core\Exception
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Core\Exception(__('Could not rebuild index for undefined product'));
        }
        $ids = array($id);
        foreach ($this->_storeManager->getStores() as $store) {
            $this->_removeDeletedProducts($ids, $store->getId());
            if (isset($ids[0])) {
                $this->_reindexSingleProduct($store->getId(), $ids[0]);
            }
        }
        return $this;
    }
}
