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
 * Class Full reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Product\Flat\Action
 */
class Full extends \Magento\Catalog\Model\Indexer\Product\Flat\AbstractAction
{
    /**
     * Execute full reindex action
     *
     * @param null|array $ids
     *
     * @return \Magento\Catalog\Model\Indexer\Product\Flat\Action\Full
     * @throws \Magento\Exception
     * @throws \Exception
     */
    public function execute($ids = null)
    {
        try {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->_reindex($store->getId());
            }
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $this;
    }
}
