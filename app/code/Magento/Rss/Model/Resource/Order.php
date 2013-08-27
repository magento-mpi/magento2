<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order Rss Resource Model
 *
 * @category    Magento
 * @package     Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Model_Resource_Order
{
    /**
     * Retrieve order comments
     *
     * @param int $orderId
     * @return array
     */
    public function getAllCommentCollection($orderId)
    {
        /** @var $res Magento_Core_Model_Resource */
        $res = Mage::getSingleton('Magento_Core_Model_Resource');
        $read = $res->getConnection('core_read');

        $fields = array(
            'notified' => 'is_customer_notified',
            'comment',
            'created_at',
        );
        $commentSelects = array();
        foreach (array('invoice', 'shipment', 'creditmemo') as $entityTypeCode) {
            $mainTable  = $res->getTableName('sales_flat_' . $entityTypeCode);
            $slaveTable = $res->getTableName('sales_flat_' . $entityTypeCode . '_comment');
            $select = $read->select()
                ->from(array('main' => $mainTable), array(
                    'entity_id' => 'order_id',
                    'entity_type_code' => new Zend_Db_Expr("'$entityTypeCode'")
                ))
                ->join(array('slave' => $slaveTable), 'main.entity_id = slave.parent_id', $fields)
                ->where('main.order_id = ?', $orderId);
            $commentSelects[] = '(' . $select . ')';
        }
        $select = $read->select()
            ->from($res->getTableName('sales_flat_order_status_history'), array(
                'entity_id' => 'parent_id',
                'entity_type_code' => new Zend_Db_Expr("'order'")
            ) + $fields)
            ->where('parent_id = ?', $orderId)
            ->where('is_visible_on_front > 0');
        $commentSelects[] = '(' . $select . ')';

        $commentSelect = $read->select()
            ->union($commentSelects, Zend_Db_Select::SQL_UNION_ALL);

        $select = $read->select()
            ->from(array('orders' => $res->getTableName('sales_flat_order')), array('increment_id'))
            ->join(array('t' => $commentSelect),'t.entity_id = orders.entity_id')
            ->order('orders.created_at desc');

        return $read->fetchAll($select);
    }
}
