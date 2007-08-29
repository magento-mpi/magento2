<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Country collection
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Model_Mysql4_Region_Collection extends Varien_Data_Collection_Db
{
    protected $_regionTable;
    protected $_regionNameTable;
    protected $_countryTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('directory_read'));

        $this->_countryTable    = Mage::getSingleton('core/resource')->getTableName('directory/country');
        $this->_regionTable     = Mage::getSingleton('core/resource')->getTableName('directory/country_region');
        $this->_regionNameTable = Mage::getSingleton('core/resource')->getTableName('directory/country_region_name');

        $lang = Mage::getSingleton('core/store')->getLanguageCode();

        $this->_sqlSelect->from($this->_regionTable);
        $this->_sqlSelect->join($this->_regionNameTable, "$this->_regionNameTable.region_id=$this->_regionTable.region_id AND $this->_regionNameTable.language_code='$lang'");

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('directory/region'));
    }

    public function addCountryFilter($countryId)
    {
        if (!empty($countryId)) {
    	    if (is_array($countryId)) {
	            $this->addFieldToFilter("$this->_regionTable.country_id", array('in'=>$countryId));    		
    	    } else {
	            $this->addFieldToFilter("$this->_regionTable.country_id", $countryId);    		
    	    }
        }
        return $this;
    }

    public function addCountryCodeFilter($countryCode)
    {
        $this->_sqlSelect->joinLeft($this->_countryTable, "{$this->_regionTable}.country_id = {$this->_countryTable}.country_id");
        $this->_sqlSelect->where("{$this->_countryTable}.iso3_code = '{$countryCode}'");
        return $this;
    }

    public function addRegionCodeFilter($regionCode)
    {
        if (!empty($regionCode)) {
            if (is_array($regionCode)) {
                $this->_sqlSelect->where("{$this->_regionTable}.code IN ('".implode("','", $regionCode)."')");
            } else {
                $this->_sqlSelect->where("{$this->_regionTable}.code = '{$regionCode}'");
            }
        }
        return $this;
    }

    public function toOptionArray()
    {
        $options = $this->_toOptionArray('region_id', 'name', array('title'=>'iso2_code'));
        if (count($options)>0) {
            array_unshift($options, array('title'=>null, 'value'=>'0', 'label'=>__('')));
        }
        return $options;
    }
}
