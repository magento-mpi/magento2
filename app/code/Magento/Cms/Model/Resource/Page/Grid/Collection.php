<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource\Page\Grid;

use Magento\Cms\Model\Resource\Page\Collection as PageCollection;

/**
 * CMS page collection
 */
class Collection extends PageCollection
{
    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('page_id');
        $connection = $this->getConnection();
        if (count($items)) {
            $select = $connection->select()->from(['cps' => $this->getTable('cms_page_store')])
                ->where('cps.page_id IN (?)', $items);
            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $storeIds = [];
                    $pageId = $item->getData('page_id');
                    if (!isset($result[$pageId])) {
                        continue;
                    }
                    $storeIds[] = $result[$pageId];
                    if ($result[$pageId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData('page_id')];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storeIds);
                }
            }
        }
        $this->_previewFlag = true;
        return parent::_afterLoad();
    }
}
