<?php

class Mage_Directory_Model_Mysql4_Region extends Mage_Directory_Model_Region 
{
    static protected $_regionTable;
    static protected $_regionNameTable;

    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    static protected $_write;

    public function __construct($data=array()) 
    {
        parent::__construct($data);
        
        $this->_regionTable     = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_regionNameTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region_name');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('customer_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('customer_write');
    }

    public function load($regionId)
    {
        $lang = Mage::getSingleton('core/store')->getLanguageCode();
        
        $select = $this->_read->select()->from($this->_regionTable)
            ->where($this->_read->quoteInto($this->_regionTable.".region_id=?", $regionId))
            ->join($this->_regionNameTable, $this->_regionNameTable.'.region_id='.$this->_regionTable.'.region_id 
                AND '.$this->_regionNameTable.".language_code='$lang'");

        $this->setData($this->_read->fetchRow($select));
        return $this;
    }
}
