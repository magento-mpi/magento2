<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales order tax resource model
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\Resource\Sales\Order\Tax;

class Item extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('sales_order_tax_item', 'tax_item_id');
    }

    /**
     * Get Tax Items with order tax information
     *
     * @param int $item_id
     * @return array
     */
    public function getTaxItemsByItemId($item_id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('item' => $this->getTable('sales_order_tax_item')), array('tax_id', 'tax_percent'))
            ->join(
                array('tax' => $this->getTable('sales_order_tax')),
                'item.tax_id = tax.tax_id',
                array('title', 'percent', 'base_amount')
            )
            ->where('item_id = ?', $item_id);

        return $adapter->fetchAll($select);
    }
}
