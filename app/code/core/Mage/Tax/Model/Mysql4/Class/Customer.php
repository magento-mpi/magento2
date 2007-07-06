<?php
/**
 * Tax class customer resource
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Class_Customer
{

    /**
     * resource tables
     */
    protected $_classCustomerTable;

    protected $_classCustomerGroupTable;

    /**
     * resources
     */
    protected $_write;

    protected $_read;


    public function __construct()
    {
        $this->_classCustomerTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_customer');
        $this->_classCustomerGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_customer_group');

        $this->_read = Mage::getSingleton('core/resource')->getConnection('tax_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('tax_write');
    }

    public function load($classId)
    {
        #
    }

    public function save($classObject)
    {
        #
    }

    public function delete($classObject)
    {
        #
    }

    public function saveGroup($groupObject)
    {
        #
    }

    public function deleteGroup($groupObject)
    {
        #
    }
}