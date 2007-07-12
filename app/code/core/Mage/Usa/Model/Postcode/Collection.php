<?php
class Mage_Usa_Model_Mysql4_Postcode_Collection extends Varien_Data_Collection_Db
{
    protected $_postcodeTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('usa_read'));

        $this->_postcodeTable = Mage::getSingleton('core/resource')->getTableName('usa/postcode');

        $this->_sqlSelect->from($this->_postcodeTable);
    }

    public function setRegionFilter($regionId)
    {
        $this->_sqlSelect->where("{$this->_postcodeTable}.region_id = '{$regionId}'");
    }
}