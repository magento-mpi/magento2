<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource;

/**
 * Flat sales order resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Order extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_resource';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'resource';

    /**
     * Is grid
     *
     * @var bool
     */
    protected $_grid = true;

    /**
     * Use increment id
     *
     * @var bool
     */
    protected $_useIncrementId = true;

    /**
     * Entity code for increment id
     *
     * @var string
     */
    protected $_entityCodeForIncrementId = 'order';

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return $this
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter = $this->getReadConnection();
        $ifnullFirst = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $ifnullLast = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatAddress = $adapter->getConcatSql(array($ifnullFirst, $adapter->quote(' '), $ifnullLast));
        $this->addVirtualGridColumn(
            'billing_name',
            'sales_flat_order_address',
            array('billing_address_id' => 'entity_id'),
            $concatAddress
        )->addVirtualGridColumn(
            'shipping_name',
            'sales_flat_order_address',
            array('shipping_address_id' => 'entity_id'),
            $concatAddress
        );

        return $this;
    }

    /**
     * Count existent products of order items by specified product types
     *
     * @param int $orderId
     * @param array $productTypeIds
     * @param bool $isProductTypeIn
     * @return array
     */
    public function aggregateProductsByTypes($orderId, $productTypeIds = array(), $isProductTypeIn = false)
    {
        $adapter = $this->getReadConnection();
        $select = $adapter->select()->from(
            array('o' => $this->getTable('sales_flat_order_item')),
            array('o.product_type', new \Zend_Db_Expr('COUNT(*)'))
        )->joinInner(
            array('p' => $this->getTable('catalog_product_entity')),
            'o.product_id=p.entity_id',
            array()
        )->where(
            'o.order_id=?',
            $orderId
        )->group(
            'o.product_type'
        );
        if ($productTypeIds) {
            $select->where(sprintf('(o.product_type %s (?))', $isProductTypeIn ? 'IN' : 'NOT IN'), $productTypeIds);
        }
        return $adapter->fetchPairs($select);
    }

    /**
     * Retrieve order_increment_id by order_id
     *
     * @param int $orderId
     * @return string
     */
    public function getIncrementId($orderId)
    {
        $adapter = $this->getReadConnection();
        $bind = array(':entity_id' => $orderId);
        $select = $adapter->select()->from(
            $this->getMainTable(),
            array("increment_id")
        )->where(
            'entity_id = :entity_id'
        );
        return $adapter->fetchOne($select, $bind);
    }
}
