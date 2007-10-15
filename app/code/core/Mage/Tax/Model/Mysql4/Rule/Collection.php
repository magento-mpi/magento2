<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule collection
 *
 * @category   Mage
 * @package    Mage_Tax
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
        
        $this->_setIdFieldName('tax_rule_id');
        $this->_classTable = $resource->getTableName('tax/tax_class');
        $this->_rateTypeTable = $resource->getTableName('tax/tax_rate_type');
        $this->_ruleTable = $resource->getTableName('tax/tax_rule');

        $this->_sqlSelect->from($this->_ruleTable);
    }

    function load($printQuery = false, $logQuery = false)
    {
        $this->_sqlSelect->joinLeft(array('cct' => $this->_classTable), "cct.class_id = {$this->_ruleTable}.tax_customer_class_id AND cct.class_type = 'CUSTOMER'", array('customer_class' => 'class_name'));
        $this->_sqlSelect->joinLeft(array('pct' => $this->_classTable), "pct.class_id = {$this->_ruleTable}.tax_product_class_id AND pct.class_type = 'PRODUCT'", array('product_class' => 'class_name'));
        $this->_sqlSelect->joinLeft($this->_rateTypeTable, "{$this->_rateTypeTable}.type_id = {$this->_ruleTable}.tax_rate_type_id", array('type_name' => 'type_name'));
        return parent::load($printQuery, $logQuery);
    }
}