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
 * Class Full reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Product\Price\Action
 */
class Full extends \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
{
    /**
     * @param null|array $ids
     *
     * @return \Magento\Catalog\Model\Indexer\Product\Price\Action\Full|mixed
     * @throws \Magento\Exception
     * @throws \Exception
     */
    public function execute($ids = null)
    {
        try {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->_logger->log('Full reindex for store - ' . $store->getId() . '');
            }
        } catch (\Exception $e) {
            throw new \Magento\Core\Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $this;
    }
}
