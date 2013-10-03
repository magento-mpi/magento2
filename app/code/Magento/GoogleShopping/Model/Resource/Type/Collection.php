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
namespace Magento\GoogleShopping\Model\Resource\Type;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Magento\GoogleShopping\Model\Type', 'Magento\GoogleShopping\Model\Resource\Type');
    }

    /**
     * Init collection select
     *
     * @return \Magento\GoogleShopping\Model\Resource\Type\Collection
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
       $paginatorAdapter = new \Zend_Paginator_Adapter_DbSelect($this->getSelect());
       return $paginatorAdapter->getCountSelect();
   }

    /**
     * Add total count of Items for each type
     *
     * @return \Magento\GoogleShopping\Model\Resource\Type\Collection
     */
    public function addItemsCount()
    {
        $this->getSelect()
            ->joinLeft(
                array('items'=>$this->getTable('googleshopping_items')),
                'main_table.type_id=items.type_id',
                array('items_total' => new \Zend_Db_Expr('COUNT(items.item_id)')))
            ->group('main_table.type_id');
        return $this;
    }

    /**
     * Add country ISO filter to collection
     *
     * @param string $iso Two-letter country ISO code
     * @return \Magento\GoogleShopping\Model\Resource\Type\Collection
     */
    public function addCountryFilter($iso)
    {
        $this->getSelect()->where('target_country=?', $iso);
        return $this;
    }

    /**
     * Join Attribute Set data
     *
     * @return \Magento\GoogleShopping\Model\Resource\Type\Collection
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
