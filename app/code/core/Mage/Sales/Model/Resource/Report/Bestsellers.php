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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bestsellers report resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Report_Bestsellers extends Mage_Sales_Model_Resource_Report_Abstract
{
    const AGGREGATION_DAILY   = 'daily';
    const AGGREGATION_MONTHLY = 'monthly';
    const AGGREGATION_YEARLY  = 'yearly';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/bestsellers_aggregated_' . self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Bestsellers
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from = $this->_dateToUtc($from);
        $to = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $adapter = $this->_getWriteAdapter();
        //$this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = new Zend_Db_Expr($adapter->getDateAddSql('source_table.created_at', $this->_getStoreTimezoneUtcOffset(), Varien_Db_Adapter_Interface::INTERVAL_HOUR));
            $ifnullProductNameValue = $adapter->getCheckSql('product_name.value IS NULL', 'product_default_name.value', 'product_name.value');
            $ifnullProductPriceValue = new Zend_Db_Expr($adapter->getCheckSql('product_price.value IS NULL', 'product_default_price.value', 'product_price.value'));
            $ifnullSourcetableToGlobalRate = new Zend_Db_Expr($adapter->getCheckSql('source_table.base_to_global_rate IS NULL', 0, 'source_table.base_to_global_rate'));

            $columns = array(
                'period'                         => $periodExpr,
                'store_id'                       => 'source_table.store_id',
                'product_id'                     => 'order_item.product_id',
                'product_name'                   => "MIN({$ifnullProductNameValue})",
                'product_price'                  => "MIN({$ifnullProductPriceValue}) * MIN({$ifnullSourcetableToGlobalRate})",
                'qty_ordered'                    => 'SUM(order_item.qty_ordered)',
            );

            $select = $adapter->select();

            $select->from(array('source_table' => $this->getTable('sales/order')), $columns)
                ->joinInner(
                    array('order_item' => $this->getTable('sales/order_item')),
                    'order_item.order_id = source_table.entity_id',
                    array()
                )
                ->where('source_table.state <> ?', Mage_Sales_Model_Order::STATE_CANCELED);


            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product $product */
            $product = Mage::getResourceSingleton('catalog/product');

            $productTypes = array(
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
                Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
            );
            
            $joinExpr = array(
                'product.entity_id = order_item.product_id',
                $adapter->quoteInto('product.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product.type_id NOT IN(?)', $productTypes)
            );
            $joinExpr = implode(' AND ', $joinExpr);
            $select->joinInner(
                array('product' => $this->getTable('catalog/product')),
                $joinExpr,
                array()
            );

            // join product attributes Name & Price
            $attr = $product->getAttribute('name');
            $joinExprProductName = array(
                'product_name.entity_id = product.entity_id',
                'product_name.store_id = source_table.store_id',
                $adapter->quoteInto('product_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductName = implode(' AND ', $joinExprProductName);
            $joinExprProductDefaultName = array(
                'product_default_name.entity_id = product.entity_id',
                'product_default_name.store_id = 0',
                $adapter->quoteInto('product_default_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductDefaultName = implode(' AND ', $joinExprProductDefaultName);
            $select->joinLeft(array('product_name' => $attr->getBackend()->getTable()),
                $joinExprProductName,
                array()
            )
            ->joinLeft(array('product_default_name' => $attr->getBackend()->getTable()),
                $joinExprProductDefaultName,
                array()
            );

            $attr = $product->getAttribute('price');
            $joinExprProductPrice = array(
                'product_price.entity_id = product.entity_id',
                'product_price.store_id = source_table.store_id',
                $adapter->quoteInto('product_price.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductPrice = implode(' AND ', $joinExprProductPrice);
            
            $joinExprProductDefPrice = array(
                'product_default_price.entity_id = product.entity_id',
                'product_default_price.store_id = 0',
                $adapter->quoteInto('product_default_price.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductDefPrice = implode(' AND ', $joinExprProductDefPrice);
            $select->joinLeft(array('product_price' => $attr->getBackend()->getTable()),
                $joinExprProductPrice,
                array()
            )
            ->joinLeft(array('product_default_price' => $attr->getBackend()->getTable()),
                $joinExprProductDefPrice,
                array()
            );

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'source_table.created_at'));
            }
            $select->group(array(
                $periodExpr,
                'source_table.store_id',
                'order_item.product_id'
            ));

            $select->useStraightJoin();  // important!

            $sql = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($sql);

            $columns = array(
                'period'                         => 'period',
                'store_id'                       => new Zend_Db_Expr('0'),
                'product_id'                     => 'product_id',
                'product_name'                   => 'MIN(product_name)',
                'product_price'                  => 'MIN(product_price)',
                'qty_ordered'                    => 'SUM(qty_ordered)',
            );

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where('store_id <> 0');

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array(
                'period',
                'product_id'
            ));

            $sql = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($sql);


            // update rating
            $this->_updateRatingPos(self::AGGREGATION_DAILY);
            $this->_updateRatingPos(self::AGGREGATION_MONTHLY);
            $this->_updateRatingPos(self::AGGREGATION_YEARLY);


            $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE);
        } catch (Exception $e) {
            //$this->_getWriteAdapter()->rollBack();
            throw $e;
        }

        //$this->_getWriteAdapter()->commit();
        return $this;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of Mage_Sales_Model_Mysql4_Report_Bestsellers::AGGREGATION_XXX constants
     * @return Mage_Sales_Model_Resource_Report_Bestsellers
     */
    public function _updateRatingPos($aggregation)
    {
        $aggregationTable = $this->getTable('sales/bestsellers_aggregated_' . $aggregation);
        $resourceHelper = Mage::getResourceHelper('sales')
            ->setMainTableName($this->getMainTable())
            ->setAggregationAliases(array(
                'daily'   => self::AGGREGATION_DAILY,
                'monthly' => self::AGGREGATION_MONTHLY,
                'yearly'  => self::AGGREGATION_YEARLY
            ))
            ->getBestsellersReportUpdateRatingPos($aggregation, $aggregationTable);

        return $this;
    }
}
