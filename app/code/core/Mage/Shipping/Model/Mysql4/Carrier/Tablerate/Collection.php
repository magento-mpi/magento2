<?php
/**
 * Shipping table rates collection
 *
 * @package     Mage
 * @subpackage  Shipping
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Shipping_Model_Mysql4_Carrier_Tablerate_Collection extends Varien_Data_Collection_Db
{
    protected $_shipTable;
    protected $_countryTable;
    protected $_regionTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('shipping_read'));
        $this->_shipTable = Mage::getSingleton('core/resource')->getTableName('shipping/tablerate');
        $this->_countryTable = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $this->_regionTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_sqlSelect->from(array("s" => $this->_shipTable))
            ->joinLeft(array("c" => $this->_countryTable), 'c.country_id = s.dest_country_id', 'iso3_code AS dest_country')
            ->joinLeft(array("r" => $this->_regionTable), 'r.region_id = s.dest_region_id', 'code AS dest_region')
            ->order(array("dest_country", "dest_region", "dest_zip"));

        return $this;
    }

    public function setWebsiteFilter($websiteId)
    {
        $this->_sqlSelect->where("website_id = ?", $websiteId);

        return $this;
    }

    public function setConditionFilter($conditionName)
    {
    	$this->_sqlSelect->where("condition_name = ?", $conditionName);

    	return $this;
    }

    public function setCountryFilter($countryId)
    {
        $this->_sqlSelect->where("dest_country_id = ?", $countryId);

        return $this;
    }
}