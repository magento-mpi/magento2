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
        
        self::$_regionTable     = Mage::registry('resources')->getTableName('directory', 'country_region');
        self::$_regionNameTable = Mage::registry('resources')->getTableName('directory', 'country_region_name');
        self::$_read = Mage::registry('resources')->getConnection('customer_read');
        self::$_write = Mage::registry('resources')->getConnection('customer_write');
    }

    public function load($regionId)
    {
        $lang = Mage::registry('website')->getLanguage();
        
        $select = self::$_read->select()->from(self::$_regionTable)
            ->where(self::$_read->quoteInto(self::$_regionTable.".region_id=?", $regionId))
            ->join(self::$_regionNameTable, self::$_regionNameTable.'.region_id='.self::$_regionTable.'.region_id 
                AND '.self::$_regionNameTable.".language_code='$lang'");

        $this->setData(self::$_read->fetchRow($select));
        return $this;
    }
}
