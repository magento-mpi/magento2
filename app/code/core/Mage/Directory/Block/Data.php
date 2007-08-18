<?php
/**
 * Directory data block
 *
 * @package     Mage
 * @subpackage  Directory
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Directory_Block_Data extends Mage_Core_Block_Template
{
    public function getLoadrRegionUrl()
    {
        return $this->getUrl('directory/json/childRegion');
    }
    
    public function getCountryCollection()
    {
        $collection = $this->getData('country_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }
        return $collection;
    }
    
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('country_id')
            ->setId('country')
            ->setTitle(__('Country'))
            ->setClass('validate-select')
            ->setValue($this->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->getHtml();
    }

    public function getRegionCollection()
    {
        $collection = $this->getData('region_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getCountryId())
                ->load();
                
            $this->setData('region_collection', $collection);
        }
        return $collection;
    }

    
    public function getRegionHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('region')
            ->setTitle(__('State/Province'))
            ->setId('state')
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray())
            ->getHtml();
    }
    
    public function getCountryId()
    {
        $countryId = $this->getData('country_id');
        if (is_null($countryId)) {
            $countryId = (int) Mage::getStoreConfig('general/country/default');
        }
        return $countryId;
    }
}
