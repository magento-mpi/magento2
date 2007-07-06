<?php
/**
 * Tax rule resource
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rule
{

    /**
     * resource tables
     */
    protected $_classCustomerTable;

    protected $_classCustomerGroupTable;

    protected $_classProductTable;

    protected $_classProductGroupTable;

    protected $_rateTable;

    protected $_rateValueTable;

    protected $_ruleTable;

    /**
     * resources
     */
    protected $_write;

    protected $_read;


    public function __construct()
    {
        $this->_classCustomerTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_customer');
        $this->_classCustomerGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_customer_group');
        $this->_classProductTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_product');
        $this->_classProductGroupTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_class_product_group');
        $this->_rateTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate');
        $this->_rateValueTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate_value');
        $this->_ruleTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rule');

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
}