<?php
/**
 * Currency Mysql4 collection model
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Mysql4_Currency_Collection extends Varien_Data_Collection_Db 
{
    protected $_currencyTable;
    protected $_currencyNameTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('directory_read'));
        $this->_currencyTable       = Mage::registry('resources')->getTableName('directory_resource', 'currency');
        $this->_currencyNameTable   = Mage::registry('resources')->getTableName('directory_resource', 'currency_name');
        
        $this->_sqlSelect->from($this->_currencyTable);
        $this->_sqlSelect->join($this->_currencyNameTable, 
            "$this->_currencyNameTable.currency_code=$this->_currencyTable.currency_code");
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory', 'currency'));
    }
    
    /**
     * Set language condition by name table
     *
     * @param   string $lang
     * @return  Varien_Data_Collection_Db
     */
    public function addLanguageFilter($lang=null)
    {
        if (is_null($lang)) {
            $lang = Mage::registry('website')->getLanguage();
        }
        $this->addFilter('language', "$this->_currencyNameTable.language_code='$lang'", 'string');
        return $this;
    }
    
    /**
     * Add currency code condition
     *
     * @param   string $code
     * @return  Varien_Data_Collection_Db
     */
    public function addCodeFilter($code)
    {
        if (is_array($code)) {
            $this->addFilter("codes", 
                $this->getConnection()->quoteInto("$this->_currencyTable.currency_code IN (?)", $code), 
                'string'
            );
        }
        else {
            $this->addFilter("code_$code", "$this->_currencyTable.currency_code='$code'", 'string');
        }
        return $this;
    }
}
