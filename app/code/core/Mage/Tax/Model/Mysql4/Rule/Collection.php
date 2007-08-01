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
    protected $_classTable;

    protected $_rateTypeTable;

    protected $_ruleTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_classTable = $resource->getTableName('tax/tax_class');
        $this->_rateTypeTable = $resource->getTableName('tax/tax_rate_type');
        $this->_ruleTable = $resource->getTableName('tax/tax_rule');

        $this->_sqlSelect->from($this->_ruleTable);
    }

    function load($printQuery = false, $logQuery = false)
    {
        $this->_sqlSelect->joinLeft(array('cct' => $this->_classTable), "cct.class_id = {$this->_ruleTable}.tax_customer_class_id AND cct.class_type = 'CUSTOMER'", array('customer_class' => 'class_name'));
        $this->_sqlSelect->joinLeft(array('pct' => $this->_classTable), "pct.class_id = {$this->_ruleTable}.tax_product_class_id AND pct.class_type = 'PRODUCT'", array('product_class' => 'class_name'));
        $this->_sqlSelect->joinLeft($this->_rateTypeTable, "{$this->_rateTypeTable}.type_id = {$this->_ruleTable}.tax_rate_id", array('type_name' => 'type_name'));
        return parent::load($printQuery, $logQuery);
    }
}