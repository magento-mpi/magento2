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
 * GoogleShopping Item Types collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Resource_Type_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('Magento_GoogleShopping_Model_Type', 'Magento_GoogleShopping_Model_Resource_Type');
    }

    /**
     * Init collection select
     *
     * @return Magento_GoogleShopping_Model_Resource_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinAttributeSet();
        return $this;
    }

   /**
    * Get SQL for get record count
    *
    * @return \Magento\DB\Select
    */
   public function getSelectCountSql()
   {
       $this->_renderFilters();
       $paginatorAdapter = new Zend_Paginator_Adapter_DbSelect($this->getSelect());
       return $paginatorAdapter->getCountSelect();
   }

    /**
     * Add total count of Items for each type
     *
     * @return Magento_GoogleShopping_Model_Resource_Type_Collection
     */
    public function addItemsCount()
    {
        $this->getSelect()
            ->joinLeft(
                array('items'=>$this->getTable('googleshopping_items')),
                'main_table.type_id=items.type_id',
                array('items_total' => new Zend_Db_Expr('COUNT(items.item_id)')))
            ->group('main_table.type_id');
        return $this;
    }

    /**
     * Add country ISO filter to collection
     *
     * @param string $iso Two-letter country ISO code
     * @return Magento_GoogleShopping_Model_Resource_Type_Collection
     */
    public function addCountryFilter($iso)
    {
        $this->getSelect()->where('target_country=?', $iso);
        return $this;
    }

    /**
     * Join Attribute Set data
     *
     * @return Magento_GoogleShopping_Model_Resource_Type_Collection
     */
    protected function _joinAttributeSet()
    {
        $this->getSelect()
            ->join(
                array('set'=>$this->getTable('eav_attribute_set')),
                'main_table.attribute_set_id=set.attribute_set_id',
                array('attribute_set_name' => 'set.attribute_set_name'));
        return $this;
    }
}
