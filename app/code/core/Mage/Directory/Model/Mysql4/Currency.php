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

        $this->_currencyTable       = Mage::getSingleton('core/resource')->getTableName('directory/currency');
        $this->_currencyNameTable   = Mage::getSingleton('core/resource')->getTableName('directory/currency_name');
        $this->_currencyRateTable   = Mage::getSingleton('core/resource')->getTableName('directory/currency_rate');
        $this->_countryCurrencyTable= Mage::getSingleton('core/resource')->getTableName('directory/country_currency');
        
        $this->_read = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('sales_write');
    }
    
    public function load($code, $lang=null)
    {
        if (is_null($lang)) {
            $lang = Mage::getSingleton('core/store')->getLanguageCode();
        }
        
        if ($this->_read) {
            $select = $this->_read->select()
                ->from($this->_currencyTable)
                ->join($this->_currencyNameTable, "$this->_currencyNameTable.currency_code=$this->_currencyTable.currency_code")
                ->where($this->_currencyTable.'.currency_code=?', $code)
                ->where($this->_currencyNameTable.'.language_code=?', $lang);
            $result = $this->_read->fetchRow($select);
            return $result;
        }
        return array();
    }
    
    public function save()
    {
        
    }
    
    public function delete()
    {
        
    }
    
    public function getRate($currencyFrom, $currencyTo)
    {
        if ($currencyFrom instanceof Mage_Directory_Model_Currency) {
            $currencyFrom = $currencyFrom->getCode();
        }
        
        if ($currencyTo instanceof Mage_Directory_Model_Currency) {
            $currencyTo = $currencyTo->getCode();
        }
        
        if ($currencyFrom == $currencyTo) {
            return 1;
        }
        
        if (!isset($this->_rateCache[$currencyFrom][$currencyTo])) {
            $select = $this->_read->select()
                ->from($this->_currencyRateTable, 'rate')
                ->where('currency_from=?', strtoupper($currencyFrom))
                ->where('currency_to=?', strtoupper($currencyTo));
                
            $_rateCache[$currencyFrom][$currencyTo] = $this->_read->fetchOne($select);
        }
        return $_rateCache[$currencyFrom][$currencyTo];
    }
}
