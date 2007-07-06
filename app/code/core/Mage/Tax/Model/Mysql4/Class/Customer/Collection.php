<?php
/**
 * Tax class customer collection
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Class_Customer_Collection extends Varien_Data_Collection_Db
{
    protected $_classCustomerTable;

    protected $_classCustomerGroupTable;

    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_classCustomerTable = $resource->getTableName('tax/tax_class_customer');
        $this->_classCustomerGroupTable = $resource->getTableName('tax/tax_class_customer_group');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tax/tax'));
    }

    function load()
    {
        $this->_sqlSelect->from($this->_classCustomerTable);

        parent::load();
        return $this;
    }

    function loadGroups()
    {
        $this->_sqlSelect->from($this->_classCustomerGroupTable);

        parent::load();
        return $this;
    }
}