<?php
/**
 * Tax rule collection
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rule_Collection extends Varien_Data_Collection_Db
{
    protected $_classCustomerTable;

    protected $_classCustomerGroupTable;

    protected $_classProductTable;

    protected $_classProductGroupTable;

    protected $_rateTable;

    protected $_rateValueTable;

    protected $_ruleTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_classCustomerTable = $resource->getTableName('tax/tax_class_customer');
        $this->_classCustomerGroupTable = $resource->getTableName('tax/tax_class_customer_group');
        $this->_classProductTable = $resource->getTableName('tax/tax_class_product');
        $this->_classProductGroupTable = $resource->getTableName('tax/tax_class_product_group');
        $this->_rateTable = $resource->getTableName('tax/tax_rate');
        $this->_rateValueTable = $resource->getTableName('tax/tax_rate_value');
        $this->_ruleTable = $resource->getTableName('tax/tax_rule');

        $this->_sqlSelect->from($this->_ruleTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tax/tax'));
    }
}