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
     * Execute Full reindex
     *
     * @param array|int|null $ids
     * @return $this
     * @throws \Magento\Catalog\Exception
     */
    public function execute($ids = null)
    {
        try {
            $this->_useIdxTable(true);
            $this->_emptyTable($this->_getIdxTable());
            $this->_prepareWebsiteDateTable();
            $this->_prepareTierPriceIndex();
            $this->_prepareGroupPriceIndex();

            foreach ($this->getTypeIndexers() as $indexer) {
                $indexer->reindexAll();
            }
            $this->_syncData();

        } catch (\Exception $e) {
            throw new \Magento\Catalog\Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $this;
    }
}
