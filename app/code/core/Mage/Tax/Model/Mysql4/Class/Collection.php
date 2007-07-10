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

class Mage_Tax_Model_Mysql4_Class_Collection extends Varien_Data_Collection_Db
{
    protected $_classTable;

    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_classTable = $resource->getTableName('tax/tax_class');

        $this->_sqlSelect->from($this->_classTable);
    }

    public function setClassTypeFilter($classType)
    {
        $this->_sqlSelect->where("{$this->_classTable}.class_type = ?", $classType);
        return $this;
    }
}