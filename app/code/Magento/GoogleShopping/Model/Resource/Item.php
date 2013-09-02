<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Item resource model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Resource_Item extends Magento_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('googleshopping_items', 'item_id');
    }

    /**
     * Load Item model by product
     *
     * @param Magento_GoogleShopping_Model_Item $model
     * @return Magento_GoogleShopping_Model_Resource_Item
     */
    public function loadByProduct($model)
    {
        if (!($model->getProduct() instanceof Magento_Object)) {
            return $this;
        }

        $product = $model->getProduct();
        $productId = $product->getId();
        $storeId = $model->getStoreId() ? $model->getStoreId() : $product->getStoreId();

        $read = $this->_getReadAdapter();
        $select = $read->select();

        if ($productId !== null) {
            $select->from($this->getMainTable())
                ->where("product_id = ?", $productId)
                ->where('store_id = ?', (int)$storeId);

            $data = $read->fetchRow($select);
            $data = is_array($data) ? $data : array();
            $model->addData($data);
        }
        return $this;
    }
}
