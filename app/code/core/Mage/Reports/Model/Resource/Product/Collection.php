<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Products Report collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    const SELECT_COUNT_SQL_TYPE_CART= 1;

    /**
     * Product entity identifier
     *
     * @var int
     */
    protected $_productEntityId;

    /**
     * Product entity table name
     *
     * @var string
     */
    protected $_productEntityTableName;

    /**
     * Product entity type identifier
     *
     * @var int
     */
    protected $_productEntityTypeId;

    /**
     * select count
     *
     * @var int
     */
    protected $_selectCountSqlType       = 0;

    /**
     * Init main class options
     *
     */
    public function __construct()
    {
        $product = Mage::getResourceSingleton('catalog/product');
        /* @var $product Mage_Catalog_Model_Entity_Product */
        $this->setProductEntityId($product->getEntityIdField());
        $this->setProductEntityTableName($product->getEntityTable());
        $this->setProductEntityTypeId($product->getTypeId());

        parent::__construct();
    }
    /**
     * Set Type for COUNT SQL Select
     *
     * @param int $type
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setSelectCountSqlType($type)
    {
        $this->_selectCountSqlType = $type;
        return $this;
    }

    /**
     * Set product entity id
     *
     * @param int $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityId($entityId)
    {
        $this->_productEntityId = (int)$entityId;
        return $this;
    }

    /**
     * Get product entity id
     *
     * @return int
     */
    public function getProductEntityId()
    {
        return $this->_productEntityId;
    }

    /**
     * Set product entity table name
     *
     * @param string $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityTableName($value)
    {
        $this->_productEntityTableName = $value;
        return $this;
    }

    /**
     * Get product entity table name
     *
     * @return string
     */
    public function getProductEntityTableName()
    {
        return $this->_productEntityTableName;
    }

    /**
     * Set product entity type id
     *
     * @param int $value
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setProductEntityTypeId($value)
    {
        $this->_productEntityTypeId = $value;
        return $this;
    }

    /**
     * Get product entity tyoe id
     *
     * @return int
     */
    public function getProductEntityTypeId()
    {
        return $this->_productEntityTypeId;
    }

    /**
     * Join fields
     *
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    protected function _joinFields()
    {
        $this->_totals = new Varien_Object();

        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price');

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return unknown
     */
    public function getSelectCountSql()
    {
        if ($this->_selectCountSqlType == self::SELECT_COUNT_SQL_TYPE_CART) {
            $countSelect = clone $this->getSelect();
            $countSelect->reset()
                ->from(array('quote_item_table' => $this->getTable('sales/quote_item')), 'COUNT(DISTINCT quote_item_table.product_id)')
                ->join(
                    array('quote_table' => $this->getTable('sales/quote')),
                    'quote_table.entity_id = quote_item_table.quote_id AND quote_table.is_active = 1',
                    array()
                );
            return $countSelect->__toString();
        }

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }

    /**
     * Add carts count
     *
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addCartsCount()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset();

        $countSelect->from(array('quote_items' => $this->getTable('sales/quote_item')), 'COUNT(*)')
            ->join(array('quotes' => $this->getTable('sales/quote')),
                'quotes.entity_id = quote_items.quote_id AND quotes.is_active = 1',
                array())
            ->where("quote_items.product_id = e.entity_id");

        $this->getSelect()
            ->columns(array("carts" => "({$countSelect})"))
            ->group("e.{$this->getProductEntityId()}")
            ->having('carts > ?', 0);

        return $this;
    }

    /**
     * Add orders count
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrdersCount($from = '', $to = '')
    {
        $this->getSelect()
            ->joinLeft(
                array("order_items" => $this->getTable('sales/order_item')),
                "order_items.product_id = e.{$this->getProductEntityId()}",
                array())
            ->columns(array("orders" => "COUNT(order_items2.item_id)"))
            ->group("e.{$this->getProductEntityId()}");

        $dateFilter = array("order_items2.item_id = order_items.item_id");
        if ($from != '' && $to != '') {
            $from = $this->getConnection()->quote($from);
            $to   = $this->getConnection()->quote($to);

            $dateFilter[] = sprintf('order_items2.created_at BETWEEN %s AND %s', $from, $to);
        }

        $this->getSelect()
            ->joinLeft(
                array("order_items2" => $this->getTable('sales/order_item')),
                implode(' AND ', $dateFilter),
                array()
            );

        return $this;
    }

    /**
     * Add ordered qtys
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrderedQty($from = '', $to = '')
    {
        $qtyOrderedTableName = $this->getTable('sales/order_item');
        $qtyOrderedFieldName = 'qty_ordered';

        $productIdFieldName = 'product_id';

        $compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $productJoinCondition     = array(
            $this->getConnection()->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            "e.entity_id = order_items.{$productIdFieldName}",
            $this->getConnection()->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        $dateFilter = array();
        if ($from != '' && $to != '') {
            $from = $this->getConnection()->quote($from);
            $to   = $this->getConnection()->quote($to);

            $dateFilter[] = sprintf('order.created_at BETWEEN %s AND %s', $from, $to);
        }

        $this->getSelect()->reset()->from(
            array('order_items' => $qtyOrderedTableName),
            array('ordered_qty' => "SUM(order_items.{$qtyOrderedFieldName})")
        );

         $dateFilter[] = $this->getConnection()->quoteInto(
            'order.entity_id = order_items.order_id AND order.state <> ?', Mage_Sales_Model_Order::STATE_CANCELED
         );

         $this->getSelect()
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $dateFilter),
                array())
            ->joinInner(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition))
            ->group('e.entity_id')
            ->having('ordered_qty > ?', 0);

        return $this;
    }

    /**
     * Set order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, array('carts', 'orders', 'ordered_qty'))) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    /**
     * Add views count
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addViewsCount($from = '', $to = '')
    {
        /**
         * Getting event type id for catalog_product_view event
         */
        foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $joinCondition = $this->getConnection()->quoteInto(
            'e.entity_id = table_views.object_id AND e.entity_type_id = ?', $this->getProductEntityTypeId()
        );
        $this->getSelect()->reset()
            ->from(
                array('table_views' => $this->getTable('reports/event')),
                array('views' => 'COUNT(table_views.event_id)'))
            ->join(
                array('e' => $this->getProductEntityTableName()),
                $joinCondition)
            ->where('table_views.event_type_id = ?', $productViewEvent)
            ->magicGroup('e.entity_id')
            ->order('views ' . self::SORT_ORDER_DESC)
            ->having('%s > 0', 'views');

//            ->having('views > ?', 0);
//var_dump($this->getSelect()->__toString());
        if ($from != '' && $to != '') {
            $this->getSelect()
                ->where('logged_at >= ?', $from)
                ->where('logged_at <= ?', $to);
        }

        return $this;
    }
}
