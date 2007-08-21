<?php

class Mage_Directory_Model_Mysql4_Country extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_countryTable;

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

    protected function _construct()
    {
        $this->_init('directory/country', 'country_id');

        $resource = Mage::getSingleton('core/resource');
        $this->_countryTable     = $resource->getTableName('directory/country');
        $this->_read    = $resource->getConnection('directory_read');
        $this->_write   = $resource->getConnection('directory_write');
    }

    public function getCountryIdByCode($code)
    {
        $select = $this->_read->select('country_id')
            ->from($this->_countryTable)
            ->where("iso3_code=?", $code);

        $row = $this->_read->fetchRow($select);
        return $row['country_id'];
    }
}
