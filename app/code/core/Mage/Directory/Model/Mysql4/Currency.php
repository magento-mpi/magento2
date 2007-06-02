<?php
/**
 * Currency Mysql4 resourcre model
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Mysql4_Currency
{
    protected $_currencyTable;
    protected $_currencyNameTable;
    protected $_currencyRateTable;
    protected $_countryCurrencyTable;
    
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    protected static $_rateCache;
    
    public function __construct() 
    {
        $this->_currencyTable       = Mage::registry('resources')->getTableName('directory_resource', 'currency');
        $this->_currencyNameTable   = Mage::registry('resources')->getTableName('directory_resource', 'currency_name');
        $this->_currencyRateTable   = Mage::registry('resources')->getTableName('directory_resource', 'currency_rate');
        $this->_countryCurrencyTable= Mage::registry('resources')->getTableName('directory_resource', 'country_currency');
        
        $this->_read = Mage::registry('resources')->getConnection('sales_read');
        $this->_write = Mage::registry('resources')->getConnection('sales_write');
    }
    
    public function load($code)
    {
        $select = $this->_read->select()
            ->from($this->_currencyTable)
            ->where($this->_read->quoteInto('currency_code=?', $code));
        return $this->_read->fetchRow($select);
    }
    
    public function save()
    {
        
    }
    
    public function delete()
    {
        
    }
    
    public function getRate($currencyFrom, $currencyTo)
    {
        if (!isset(self::$_rateCache[$currencyFrom][$currencyTo])) {
            $select = $this->_read->select()
                ->from($this->_currencyRateTable, 'rate')
                ->where($this->_read->quoteInto('currency_from=?', strtoupper($currencyFrom)))
                ->where($this->_read->quoteInto('currency_to=?', strtoupper($currencyTo)));
                
            $_rateCache[$currencyFrom][$currencyTo] = $this->_read->fetchOne($select);
        }
        return $_rateCache[$currencyFrom][$currencyTo];
    }
}
