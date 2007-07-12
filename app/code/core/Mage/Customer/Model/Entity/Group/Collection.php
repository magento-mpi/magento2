<?php
/**
 * Customer group collection
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Customer_Model_Entity_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;

    protected $_taxClassGroupTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('customer_read'));
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName('customer/customer_group');
        $this->_taxClassGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_group');
        $this->_sqlSelect->from($this->_groupTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('customer/group'));
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }


    public function setTaxGroupFilter($classId)
    {
        $this->_sqlSelect->joinLeft($this->_taxClassGroupTable, "{$this->_taxClassGroupTable}.class_group_id = {$this->_groupTable}.customer_group_id");
    	$this->_sqlSelect->where("{$this->_taxClassGroupTable}.class_parent_id = ?", $classId);
    	return $this;
    }

    public function setIgnoreIdFilter($indexes)
    {
        if( !count($indexes) > 0 ) {
            return $this;
        }
        $this->_sqlSelect->where('customer_group_id NOT IN(?)', $indexes);
        return $this;
    }
}