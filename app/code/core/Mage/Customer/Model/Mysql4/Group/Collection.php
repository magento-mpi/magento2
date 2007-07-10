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
class Mage_Customer_Model_Mysql4_Group_Collection extends Varien_Data_Collection_Db
{
    protected $_groupTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('customer_read'));
        $this->_groupTable = Mage::getSingleton('core/resource')->getTableName('customer/customer_group');
        $this->_sqlSelect->from($this->_groupTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('customer/group'));
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }
}