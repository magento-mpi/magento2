<?php
/**
 * Tax rate collection
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rate_Collection extends Varien_Data_Collection_Db
{
    protected $_rateTable;

    protected $_rateValueTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('tax_read'));

        $this->_rateTable = $resource->getTableName('tax/tax_rate');
        $this->_rateValueTable = $resource->getTableName('tax/tax_rate_value');

        $this->_sqlSelect->from($this->_rateTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tax/tax'));
    }
}