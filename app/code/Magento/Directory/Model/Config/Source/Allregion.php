<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Directory_Model_Config_Source_Allregion implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_countries;
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $countriesArray = Mage::getResourceModel('Magento_Directory_Model_Resource_Country_Collection')->load()
                ->toOptionArray(false);
            $this->_countries = array();
            foreach ($countriesArray as $a) {
                $this->_countries[$a['value']] = $a['label'];
            }

            $countryRegions = array();
            $regionsCollection = Mage::getResourceModel('Magento_Directory_Model_Resource_Region_Collection')->load();
            foreach ($regionsCollection as $region) {
                $countryRegions[$region->getCountryId()][$region->getId()] = $region->getDefaultName();
            }
            uksort($countryRegions, array($this, 'sortRegionCountries'));

            $this->_options = array();
            foreach ($countryRegions as $countryId=>$regions) {
                $regionOptions = array();
                foreach ($regions as $regionId=>$regionName) {
                    $regionOptions[] = array('label'=>$regionName, 'value'=>$regionId);
                }
                $this->_options[] = array('label'=>$this->_countries[$countryId], 'value'=>$regionOptions);
            }
        }
        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=>''));
        }

        return $options;
    }

    public function sortRegionCountries($a, $b)
    {
        return strcmp($this->_countries[$a], $this->_countries[$b]);
    }
}
