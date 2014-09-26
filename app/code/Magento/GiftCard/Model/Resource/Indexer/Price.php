<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Resource\Indexer;

/**
 * GiftCard product price indexer resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Price extends \Magento\Catalog\Model\Resource\Product\Indexer\Price\DefaultPrice
{
    /**
     * Prepare giftCard products prices in temporary index table
     *
     * @param int|array $entityIds  the entity ids limitation
     * @return $this
     */
    protected function _prepareFinalPriceData($entityIds = null)
    {
        $this->_prepareDefaultFinalPriceTable();

        $write = $this->_getWriteAdapter();
        $select = $write->select()->from(
            array('e' => $this->getTable('catalog_product_entity')),
            array('entity_id')
        )->join(
            array('cg' => $this->getTable('customer_group')),
            '',
            array('customer_group_id')
        );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns(
            array('website_id'),
            'cw'
        )->columns(
            array('tax_class_id' => new \Zend_Db_Expr('0'))
        )->where(
            'e.type_id = ?',
            $this->getTypeId()
        );

        // add enable products limitation
        $statusCond = $write->quoteInto('=?', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond, true);

        $allowOpenAmount = $this->_addAttributeToSelect($select, 'allow_open_amount', 'e.entity_id', 'cs.store_id');
        $openAmountMin = $this->_addAttributeToSelect($select, 'open_amount_min', 'e.entity_id', 'cs.store_id');
        //        $openAmounMax    = $this->_addAttributeToSelect($select, 'open_amount_max', 'e.entity_id', 'cs.store_id');



        $attrAmounts = $this->_getAttribute('giftcard_amounts');
        // join giftCard amounts table
        $select->joinLeft(
            array('gca' => $this->getTable('magento_giftcard_amount')),
            'gca.entity_id = e.entity_id AND gca.attribute_id = ' .
            $attrAmounts->getAttributeId() .
            ' AND (gca.website_id = cw.website_id OR gca.website_id = 0)',
            array()
        );

        $amountsExpr = 'MIN(' . $write->getCheckSql('gca.value_id IS NULL', 'NULL', 'gca.value') . ')';

        $openAmountExpr = 'MIN(' . $write->getCheckSql(
            $allowOpenAmount . ' = 1',
            $write->getCheckSql($openAmountMin . ' > 0', $openAmountMin, '0'),
            'NULL'
        ) . ')';

        $priceExpr = new \Zend_Db_Expr(
            'ROUND(' . $write->getCheckSql(
                $openAmountExpr . ' IS NULL',
                $write->getCheckSql($amountsExpr . ' IS NULL', '0', $amountsExpr),
                $write->getCheckSql(
                    $amountsExpr . ' IS NULL',
                    $openAmountExpr,
                    $write->getCheckSql($openAmountExpr . ' > ' . $amountsExpr, $amountsExpr, $openAmountExpr)
                )
            ) . ', 4)'
        );

        $select->group(
            array('e.entity_id', 'cg.customer_group_id', 'cw.website_id')
        )->columns(
            array(
                'price' => new \Zend_Db_Expr('NULL'),
                'final_price' => $priceExpr,
                'min_price' => $priceExpr,
                'max_price' => new \Zend_Db_Expr('NULL'),
                'tier_price' => new \Zend_Db_Expr('NULL'),
                'base_tier' => new \Zend_Db_Expr('NULL'),
                'group_price' => new \Zend_Db_Expr('NULL'),
                'base_group_price' => new \Zend_Db_Expr('NULL')
            )
        );

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        $this->_eventManager->dispatch(
            'prepare_catalog_product_index_select',
            array(
                'select' => $select,
                'entity_field' => new \Zend_Db_Expr('e.entity_id'),
                'website_field' => new \Zend_Db_Expr('cw.website_id'),
                'store_field' => new \Zend_Db_Expr('cs.store_id')
            )
        );

        $query = $select->insertFromSelect($this->_getDefaultFinalPriceTable());
        $write->query($query);

        return $this;
    }
}
