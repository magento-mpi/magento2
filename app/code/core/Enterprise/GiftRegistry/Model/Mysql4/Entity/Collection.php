<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry entity collection
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
    extends Enterprise_Enterprise_Model_Core_Mysql4_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry/entity', 'entity_id');
    }

    /**
     * Load collection by customer id
     *
     * @param int $id
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    public function filterByCustomerId($id)
    {
        $this->getSelect()->where('main_table.customer_id = ?', $id);
        return $this;
    }

    /**
     * Load collection by customer id
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    public function filterByActive()
    {
        $this->getSelect()->where('main_table.is_active = 1');
        return $this;
    }

    /**
     * Add registry info
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    public function addRegistryInfo()
    {
        $this->_addQtyItemsData();
        $this->_addEventData();
        $this->_addRegistrantData();

        return $this;
    }

    /**
     * Add registry quantity info
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    protected function _addQtyItemsData()
    {
        $this->getSelect()->joinLeft(
            array('item' => $this->getTable('enterprise_giftregistry/item')),
            'main_table.entity_id = item.entity_id',
            array(
                'qty' => new Zend_Db_Expr('SUM(item.qty)'),
                'qty_fulfilled' => new Zend_Db_Expr('SUM(item.qty_fulfilled)'),
                'qty_remaining' => new Zend_Db_Expr('SUM(item.qty - item.qty_fulfilled)')
            )
        );
        $this->getSelect()->group(array('main_table.entity_id'));
        return $this;
    }

    /**
     * Add event info to collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    protected function _addEventData()
    {
        $this->getSelect()->joinLeft(
            array('data' => $this->getTable('enterprise_giftregistry/data')),
            'main_table.entity_id = data.entity_id',
            array('data.event_date')
        );
        return $this;
    }

    /**
     * Add registrant info to collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Entity_Collection
     */
    protected function _addRegistrantData()
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('enterprise_giftregistry/person'), array(
                'entity_id',
                'registrants' => new Zend_Db_Expr("GROUP_CONCAT(firstname,' ',lastname)")
            ))
            ->group('entity_id');

        $this->getSelect()->joinLeft(
            array('person' => $select),
            'main_table.entity_id = person.entity_id',
            array('registrants')
        );
        return $this;
    }
}
