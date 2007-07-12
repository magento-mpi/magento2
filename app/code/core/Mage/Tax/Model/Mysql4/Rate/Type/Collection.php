<?php
/**
 * Tax rate type collection
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rate_Type_Collection extends Varien_Data_Collection_Db
{
    protected $_rateTypeTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_rateTypeTable = $resource->getTableName('tax/tax_rate_type');
        $this->_sqlSelect->from($this->_rateTypeTable);
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('type_id', 'type_name');
    }
}