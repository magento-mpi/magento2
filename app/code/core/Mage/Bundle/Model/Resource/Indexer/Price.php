<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle products Price indexer resource model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Resource_Indexer_Price extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);

        $this->beginTransaction();
        try {
            $this->_prepareBundlePrice();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareBundlePrice($entityIds);

        return $this;
    }

    /**
     * Retrieve temporary price index table name for fixed bundle products
     *
     * @return string
     */
    protected function _getBundlePriceTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog_product_index_price_bundle_idx');
        }
        return $this->getTable('catalog_product_index_price_bundle_tmp');
    }

    /**
     * Retrieve table name for temporary bundle selection prices index
     *
     * @return string
     */
    protected function _getBundleSelectionTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog_product_index_price_bundle_sel_idx');
        }
        return $this->getTable('catalog_product_index_price_bundle_sel_tmp');
    }

    /**
     * Retrieve table name for temporary bundle option prices index
     *
     * @return string
     */
    protected function _getBundleOptionTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('catalog_product_index_price_bundle_opt_idx');
        }
        return $this->getTable('catalog_product_index_price_bundle_opt_tmp');
    }

    /**
     * Prepare temporary price index table for fixed bundle products
     *
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareBundlePriceTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundlePriceTable());
        return $this;
    }

    /**
     * Prepare table structure for temporary bundle selection prices index
     *
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareBundleSelectionTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundleSelectionTable());
        return $this;
    }

    /**
     * Prepare table structure for temporary bundle option prices index
     *
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareBundleOptionTable()
    {
        $this->_getWriteAdapter()->delete($this->_getBundleOptionTable());
        return $this;
    }

    /**
     * Prepare temporary price index data for bundle products by price type
     *
     * @param int $priceType
     * @param int|array $entityIds the entity ids limitatation
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareBundlePriceByType($priceType, $entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->_getBundlePriceTable();

        $select = $write->select()
            ->from(array('e' => $this->getTable('catalog_product_entity')), array('entity_id'))
            ->join(
                array('cg' => $this->getTable('customer_group')),
                '',
                array('customer_group_id')
            );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns('website_id', 'cw')
            ->join(
                array('cwd' => $this->_getWebsiteDateTable()),
                'cw.website_id = cwd.website_id',
                array()
            )
            ->joinLeft(
                array('tp' => $this->_getTierPriceIndexTable()),
                'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id'
                . ' AND tp.customer_group_id = cg.customer_group_id',
                array()
            )
            ->where('e.type_id=?', $this->getTypeId());

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);
        if (Mage::helper('Mage_Core_Helper_Data')->isModuleEnabled('Mage_Tax')) {
            $taxClassId = $this->_addAttributeToSelect($select, 'tax_class_id', 'e.entity_id', 'cs.store_id');
        } else {
            $taxClassId = new Zend_Db_Expr('0');
        }

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC) {
            $select->columns(array('tax_class_id' => new Zend_Db_Expr('0')));
        } else {
            $select->columns(
                array('tax_class_id' => $write->getCheckSql($taxClassId . ' IS NOT NULL', $taxClassId, 0))
            );
        }

        $priceTypeCond = $write->quoteInto('=?', $priceType);
        $this->_addAttributeToSelect($select, 'price_type', 'e.entity_id', 'cs.store_id', $priceTypeCond);

        $price          = $this->_addAttributeToSelect($select, 'price', 'e.entity_id', 'cs.store_id');
        $specialPrice   = $this->_addAttributeToSelect($select, 'special_price', 'e.entity_id', 'cs.store_id');
        $specialFrom    = $this->_addAttributeToSelect($select, 'special_from_date', 'e.entity_id', 'cs.store_id');
        $specialTo      = $this->_addAttributeToSelect($select, 'special_to_date', 'e.entity_id', 'cs.store_id');
        $curentDate     = new Zend_Db_Expr('cwd.website_date');


        $specialExpr    = $write->getCheckSql(
            $write->getCheckSql(
                $specialFrom . ' IS NULL',
                '1',
                $write->getCheckSql(
                    $specialFrom . ' <= ' . $curentDate,
                    '1',
                    '0'
                )
            ) . " > 0 AND ".
            $write->getCheckSql(
                $specialTo . ' IS NULL',
                '1',
                $write->getCheckSql(
                    $specialTo . ' >= ' . $curentDate,
                    '1',
                    '0'
                )
            )
            . " > 0 AND {$specialPrice} > 0 AND {$specialPrice} < 100 ",
            $specialPrice,
            '0'
        );
        $tierExpr       = new Zend_Db_Expr("tp.min_price");

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {
            $finalPrice = $write->getCheckSql(
                $specialExpr . ' > 0',
                'ROUND(' . $price . ' * (' . $specialExpr . '  / 100), 4)',
                $price
            );
            $tierPrice = $write->getCheckSql(
                $tierExpr . ' IS NOT NULL',
                'ROUND(' . $price .' - ' . '(' . $price . ' * (' . $tierExpr . ' / 100)), 4)',
                'NULL'
            );
        } else {
            $finalPrice     = new Zend_Db_Expr("0");
            $tierPrice      = $write->getCheckSql($tierExpr . ' IS NOT NULL', '0', 'NULL');
        }

        $select->columns(array(
            'price_type'    => new Zend_Db_Expr($priceType),
            'special_price' => $specialExpr,
            'tier_percent'  => $tierExpr,
            'orig_price'    => $write->getCheckSql($price . ' IS NULL', '0', $price),
            'price'         => $finalPrice,
            'min_price'     => $finalPrice,
            'max_price'     => $finalPrice,
            'tier_price'    => $tierPrice,
            'base_tier'     => $tierPrice,
        ));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('catalog_product_prepare_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Calculate fixed bundle product selections price
     *
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _calculateBundleOptionPrice()
    {
        $write = $this->_getWriteAdapter();

        $this->_prepareBundleSelectionTable();
        $this->_calculateBundleSelectionPrice(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED);
        $this->_calculateBundleSelectionPrice(Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC);

        $this->_prepareBundleOptionTable();

        $select = $write->select()
            ->from(
                array('i' => $this->_getBundleSelectionTable()),
                array('entity_id', 'customer_group_id', 'website_id', 'option_id')
            )
            ->group(array('entity_id', 'customer_group_id', 'website_id', 'option_id', 'is_required', 'group_type'))
            ->columns(array(
                'min_price' => $write->getCheckSql('i.is_required = 1', 'MIN(i.price)', '0'),
                'alt_price' => $write->getCheckSql('i.is_required = 0', 'MIN(i.price)', '0'),
                'max_price' => $write->getCheckSql('i.group_type = 1', 'SUM(i.price)', 'MAX(i.price)'),
                'tier_price' => $write->getCheckSql('i.is_required = 1', 'MIN(i.tier_price)', '0'),
                'alt_tier_price' => $write->getCheckSql('i.is_required = 0', 'MIN(i.tier_price)', '0'),
            ));

        $query = $select->insertFromSelect($this->_getBundleOptionTable());
        $write->query($query);

        $this->_prepareDefaultFinalPriceTable();

        $minPrice  = new Zend_Db_Expr($write->getCheckSql(
            'SUM(io.min_price) = 0',
            'MIN(io.alt_price)',
            'SUM(io.min_price)'
        ) . ' + i.price');
        $maxPrice  = new Zend_Db_Expr("SUM(io.max_price) + i.price");
        $tierPrice = $write->getCheckSql(
            'MIN(i.tier_percent) IS NOT NULL',
            $write->getCheckSql(
                'SUM(io.tier_price) = 0',
                'SUM(io.alt_tier_price)',
                'SUM(io.tier_price)'
            ) . ' + MIN(i.tier_price)',
            'NULL'
        );

        $select = $write->select()
            ->from(
                array('io' => $this->_getBundleOptionTable()),
                array('entity_id', 'customer_group_id', 'website_id')
            )
            ->join(
                array('i' => $this->_getBundlePriceTable()),
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                . ' AND i.website_id = io.website_id',
                array()
            )
            ->group(array('io.entity_id', 'io.customer_group_id', 'io.website_id',
                'i.tax_class_id', 'i.orig_price', 'i.price'))
            ->columns(array('i.tax_class_id',
                'orig_price'    => 'i.orig_price',
                'price'         => 'i.price',
                'min_price'     => $minPrice,
                'max_price'     => $maxPrice,
                'tier_price'    => $tierPrice,
                'base_tier'     => 'MIN(i.base_tier)'
            ));

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);

        return $this;
    }

    /**
     * Calculate bundle product selections price by product type
     *
     * @param int $priceType
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _calculateBundleSelectionPrice($priceType)
    {
        $write = $this->_getWriteAdapter();

        if ($priceType == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED) {

            $selectionPriceValue = $write->getCheckSql(
                'bsp.selection_price_value IS NULL',
                'bs.selection_price_value',
                'bsp.selection_price_value'
            );
            $selectionPriceType = $write->getCheckSql(
                'bsp.selection_price_type IS NULL',
                'bs.selection_price_type',
                'bsp.selection_price_type'
            );
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    $selectionPriceType . ' = 1',
                    'ROUND(i.price * (' . $selectionPriceValue . ' / 100),4)',
                    $write->getCheckSql(
                        'i.special_price > 0 AND i.special_price < 100',
                        'ROUND(' . $selectionPriceValue . ' * (i.special_price / 100),4)',
                        $selectionPriceValue
                    )
                ) . '* bs.selection_qty'
            );


            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                $write->getCheckSql(
                    $selectionPriceType .' = 1',
                    'ROUND(i.base_tier - (i.base_tier * (' . $selectionPriceValue . ' / 100)),4)',
                    $write->getCheckSql(
                        'i.tier_percent > 0',
                        'ROUND(' . $selectionPriceValue
                        . ' - (' . $selectionPriceValue . ' * (i.tier_percent / 100)),4)',
                        $selectionPriceValue
                    )
                ) . ' * bs.selection_qty',
                'NULL'
            );
        } else {
            $priceExpr = new Zend_Db_Expr(
                $write->getCheckSql(
                    'i.special_price > 0 AND i.special_price < 100',
                    'ROUND(idx.min_price * (i.special_price / 100), 4)',
                    'idx.min_price'
                ) . ' * bs.selection_qty'
            );
            $tierExpr = $write->getCheckSql(
                'i.base_tier IS NOT NULL',
                'ROUND(idx.min_price * (i.base_tier / 100), 4)* bs.selection_qty',
                'NULL'
            );

        }

        $select = $write->select()
            ->from(
                array('i' => $this->_getBundlePriceTable()),
                array('entity_id', 'customer_group_id', 'website_id')
            )
            ->join(
                array('bo' => $this->getTable('catalog_product_bundle_option')),
                'bo.parent_id = i.entity_id',
                array('option_id')
            )
            ->join(
                array('bs' => $this->getTable('catalog_product_bundle_selection')),
                'bs.option_id = bo.option_id',
                array('selection_id')
            )
            ->joinLeft(
                array('bsp' => $this->getTable('catalog_product_bundle_selection_price')),
                'bs.selection_id = bsp.selection_id AND bsp.website_id = i.website_id',
                array('')
            )
            ->join(
                array('idx' => $this->getIdxTable()),
                'bs.product_id = idx.entity_id AND i.customer_group_id = idx.customer_group_id'
                . ' AND i.website_id = idx.website_id',
                array()
            )
            ->join(
                array('e' => $this->getTable('catalog_product_entity')),
                'bs.product_id = e.entity_id AND e.required_options=0',
                array()
            )
            ->where('i.price_type=?', $priceType)
            ->columns(array(
                'group_type'    => $write->getCheckSql(
                    "bo.type = 'select' OR bo.type = 'radio'",
                    '0',
                    '1'
                ),
                'is_required'   => 'bo.required',
                'price'         => $priceExpr,
                'tier_price'    => $tierExpr,
            ));

        $query = $select->insertFromSelect($this->_getBundleSelectionTable());
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary index price for bundle products
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareBundlePrice($entityIds = null)
    {
        $this->_prepareTierPriceIndex($entityIds);
        $this->_prepareBundlePriceTable();
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED, $entityIds);
        $this->_prepareBundlePriceByType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC, $entityIds);

        /**
         * Add possibility modify prices from external events
         */
        $select = $this->_getWriteAdapter()->select()
            ->join(array('wd' => $this->_getWebsiteDateTable()),
                'i.website_id = wd.website_id',
                array()
            );
        Mage::dispatchEvent('prepare_catalog_product_price_index_table', array(
            'index_table'       => array('i' => $this->_getBundlePriceTable()),
            'select'            => $select,
            'entity_id'         => 'i.entity_id',
            'customer_group_id' => 'i.customer_group_id',
            'website_id'        => 'i.website_id',
            'website_date'      => 'wd.website_date',
            'update_fields'     => array('price', 'min_price', 'max_price')
        ));

        $this->_calculateBundleOptionPrice();
        $this->_applyCustomOption();

        $this->_movePriceDataToIndexTable();

        return $this;
    }

    /**
     * Prepare percentage tier price for bundle products
     *
     * @see Mage_Catalog_Model_Resource_Product_Indexer_Price::_prepareTierPriceIndex
     *
     * @param int|array $entityIds
     * @return Mage_Bundle_Model_Resource_Indexer_Price
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $adapter = $this->_getWriteAdapter();

        // remove index by bundle products
        $select  = $adapter->select()
            ->from(array('i' => $this->_getTierPriceIndexTable()), null)
            ->join(
                array('e' => $this->getTable('catalog_product_entity')),
                'i.entity_id=e.entity_id',
                array()
            )
            ->where('e.type_id=?', $this->getTypeId());
        $query   = $select->deleteFromSelect('i');
        $adapter->query($query);

        $select  = $adapter->select()
            ->from(
                array('tp' => $this->getTable('catalog_product_entity_tier_price')),
                array('entity_id')
            )
            ->join(
                array('e' => $this->getTable('catalog_product_entity')),
                'tp.entity_id=e.entity_id',
                array()
            )
            ->join(
                array('cg' => $this->getTable('customer_group')),
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id')
            )
            ->join(
                array('cw' => $this->getTable('core_website')),
                'tp.website_id = 0 OR tp.website_id = cw.website_id',
                array('website_id')
            )
            ->where('cw.website_id != 0')
            ->where('e.type_id=?', $this->getTypeId())
            ->columns(new Zend_Db_Expr('MIN(tp.value)'))
            ->group(array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }

        $query   = $select->insertFromSelect($this->_getTierPriceIndexTable());
        $adapter->query($query);

        return $this;
    }
}
