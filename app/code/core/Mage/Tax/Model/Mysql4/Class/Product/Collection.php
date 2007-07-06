<?php
/**
 * Tax class product collection
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Class_Product_Collection extends Varien_Data_Collection_Db
{
    protected $_classProductTable;

    protected $_classProductGroupTable;

    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_classProductTable = $resource->getTableName('tax/tax_class_product');
        $this->_classProductGroupTable = $resource->getTableName('tax/tax_class_product_group');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tax/tax'));
    }

    function load()
    {
        $this->_sqlSelect->from($this->_classProductTable);

        parent::load();
        return $this;
    }

    function loadGroups()
    {
        $this->_sqlSelect->from($this->_classProductGroupTable);

        parent::load();
        return $this;
    }
}