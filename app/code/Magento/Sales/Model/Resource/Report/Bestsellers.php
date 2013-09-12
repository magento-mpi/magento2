<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bestsellers report resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Bestsellers extends Magento_Sales_Model_Resource_Report_Abstract
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
        $this->_init('sales_bestsellers_aggregated_' . self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Sales_Model_Resource_Report_Bestsellers
     * @throws Exception
     */
    public function aggregate($from = null, $to = null)
    {
        // convert input dates to UTC to be comparable with DATETIME fields in DB
        $from    = $this->_dateToUtc($from);
        $to      = $this->_dateToUtc($to);

        $this->_checkDates($from, $to);
        $adapter = $this->_getWriteAdapter();
        //$this->_getWriteAdapter()->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales_flat_order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);
            // convert dates from UTC to current admin timezone
            $periodExpr = $adapter->getDatePartSql(
                $this->getStoreTZOffsetQuery(
                    array('source_table' => $this->getTable('sales_flat_order')),
                    'source_table.created_at', $from, $to
                )
            );
            $select = $adapter->select();

            $select->group(array(
                $periodExpr,
                'source_table.store_id',
                'order_item.product_id'
            ));

            $columns = array(
                'period'                 => $periodExpr,
                'store_id'               => 'source_table.store_id',
                'product_id'             => 'order_item.product_id',
                'product_name'           => new Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                    'product_name.value',
                    'product_default_name.value'
                ))),
                'product_price'          => new Zend_Db_Expr(
                    sprintf(
                        '%s * %s',
                        new Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                            $adapter->getIfNullSql('product_price.value', 'product_default_price.value'), 0
                        ))),
                        new Zend_Db_Expr(sprintf('MIN(%s)', $adapter->getIfNullSql(
                            'source_table.base_to_global_rate',
                            '0'
                        )))
                    )
                ),
                'qty_ordered' => new Zend_Db_Expr('SUM(order_item.qty_ordered)')
            );

            $select->from(array('source_table' => $this->getTable('sales_flat_order')), $columns)
                ->joinInner(array(
                    'order_item' => $this->getTable('sales_flat_order_item')),
                    'order_item.order_id = source_table.entity_id',
                    array()
                )
                ->where('source_table.state != ?', Magento_Sales_Model_Order::STATE_CANCELED);

            /** @var Magento_Catalog_Model_Resource_Product $product */
            $product  = Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product');

            $productTypes = array(
                Magento_Catalog_Model_Product_Type::TYPE_GROUPED,
                Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
                Magento_Catalog_Model_Product_Type::TYPE_BUNDLE,
            );

            $joinExpr = array(
                'product.entity_id = order_item.product_id',
                $adapter->quoteInto('product.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product.type_id NOT IN(?)', $productTypes)
            );

            $joinExpr = implode(' AND ', $joinExpr);
            $select->joinInner(
                array('product' => $this->getTable('catalog_product_entity')),
                $joinExpr,
                array()
            );

            // join product attributes Name & Price
            $attr= $product->getAttribute('name');
            $joinExprProductName = array(
                'product_name.entity_id = product.entity_id',
                'product_name.store_id = source_table.store_id',
                $adapter->quoteInto('product_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinExprProductName = implode(' AND ', $joinExprProductName);
            $joinProductName = array(
                'product_default_name.entity_id = product.entity_id',
                'product_default_name.store_id = 0',
                $adapter->quoteInto('product_default_name.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_name.attribute_id = ?', $attr->getAttributeId())
            );
            $joinProductName = implode(' AND ', $joinProductName);
            $select->joinLeft(
                array('product_name' => $attr->getBackend()->getTable()),
                $joinExprProductName,
                array()
            )
            ->joinLeft(
                array('product_default_name' => $attr->getBackend()->getTable()),
                $joinProductName,
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

            $joinProductPrice = array(
                'product_default_price.entity_id = product.entity_id',
                'product_default_price.store_id = 0',
                $adapter->quoteInto('product_default_price.entity_type_id = ?', $product->getTypeId()),
                $adapter->quoteInto('product_default_price.attribute_id = ?', $attr->getAttributeId())
            );
            $joinProductPrice = implode(' AND ', $joinProductPrice);
            $select->joinLeft(
                array('product_price' => $attr->getBackend()->getTable()),
                $joinExprProductPrice,
                array()
            )
            ->joinLeft(
                array('product_default_price' => $attr->getBackend()->getTable()),
                $joinProductPrice,
                array()
            );

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->useStraightJoin();  // important!
            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($insertQuery);


            $columns = array(
                'period'        => 'period',
                'store_id'      => new Zend_Db_Expr(Magento_Core_Model_AppInterface::ADMIN_STORE_ID),
                'product_id'    => 'product_id',
                'product_name'  => new Zend_Db_Expr('MIN(product_name)'),
                'product_price' => new Zend_Db_Expr('MIN(product_price)'),
                'qty_ordered'   => new Zend_Db_Expr('SUM(qty_ordered)'),
            );

            $select->reset();
            $select->from($this->getMainTable(), $columns)
                ->where('store_id <> ?', 0);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(array('period', 'product_id'));
            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $adapter->query($insertQuery);

            // update rating
            $this->_updateRatingPos(self::AGGREGATION_DAILY);
            $this->_updateRatingPos(self::AGGREGATION_MONTHLY);
            $this->_updateRatingPos(self::AGGREGATION_YEARLY);
            $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE);
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of Magento_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_XXX constants
     * @return Magento_Sales_Model_Resource_Report_Bestsellers
     */
    protected function _updateRatingPos($aggregation)
    {
        $aggregationTable   = $this->getTable('sales_bestsellers_aggregated_' . $aggregation);

        $aggregationAliases = array(
            'daily'   => self::AGGREGATION_DAILY,
            'monthly' => self::AGGREGATION_MONTHLY,
            'yearly'  => self::AGGREGATION_YEARLY
        );
        Mage::getResourceHelper('Magento_Sales')->getBestsellersReportUpdateRatingPos(
            $aggregation,
            $aggregationAliases,
            $this->getMainTable(),
            $aggregationTable
        );
        return $this;
    }
}
