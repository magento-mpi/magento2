<?php

class Mage_Directory_Model_Mysql4_Region
{
    protected $_regionTable;
    protected $_regionNameTable;

    /**
     * DB read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * DB write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;

    public function __construct() 
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_regionTable     = $resource->getTableName('directory/country_region');
        $this->_regionNameTable = $resource->getTableName('directory/country_region_name');
        $this->_read    = $resource->getConnection('directory_read');
        $this->_write   = $resource->getConnection('directory_write');
    }
    
    public function getIdFieldName()
    {
        return 'region_id';
    }

    public function load(Mage_Directory_Model_Region $region, $regionId)
    {
        $lang = Mage::getSingleton('core/store')->getLanguageCode();
        
        $select = $this->_read->select()
            ->from($this->_regionTable)
            ->where($this->_regionTable.".region_id=?", $regionId)
            ->join($this->_regionNameTable, $this->_regionNameTable.'.region_id='.$this->_regionTable.'.region_id 
                AND '.$this->_regionNameTable.".language_code='$lang'");

        $region->setData($this->_read->fetchRow($select));
        return $this;
    }
}
