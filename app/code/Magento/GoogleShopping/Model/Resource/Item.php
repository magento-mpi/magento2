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
namespace Magento\GoogleShopping\Model\Resource;

class Item extends \Magento\Model\Resource\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('googleshopping_items', 'item_id');
    }

    /**
     * Load Item model by product
     *
     * @param \Magento\GoogleShopping\Model\Item $model
     * @return $this
     */
    public function loadByProduct($model)
    {
        if (!$model->getProduct() instanceof \Magento\Object) {
            return $this;
        }

        $product = $model->getProduct();
        $productId = $product->getId();
        $storeId = $model->getStoreId() ? $model->getStoreId() : $product->getStoreId();

        $read = $this->_getReadAdapter();
        $select = $read->select();

        if ($productId !== null) {
            $select->from(
                $this->getMainTable()
            )->where(
                "product_id = ?",
                $productId
            )->where(
                'store_id = ?',
                (int)$storeId
            );

            $data = $read->fetchRow($select);
            $data = is_array($data) ? $data : array();
            $model->addData($data);
        }
        return $this;
    }
}
