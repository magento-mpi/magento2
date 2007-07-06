<?php
/**
 * Tax rate resource
 *
 * @package     Mage
 * @subpackage  Tax
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tax_Model_Mysql4_Rate
{

    /**
     * resource tables
     */
    protected $_rateTable;

    protected $_rateValueTable;

    /**
     * resources
     */
    protected $_write;

    protected $_read;


    public function __construct()
    {
        $this->_rateTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate');
        $this->_rateValueTable = Mage::getSingleton('core/resource')->getTableName('tax/tax_rate_value');

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