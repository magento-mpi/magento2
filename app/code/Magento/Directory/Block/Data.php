<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory data block
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Block_Data extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_configCacheType = $configCacheType;
    }

    public function getLoadrRegionUrl()
    {
        return $this->getUrl('directory/json/childRegion');
    }

    public function getCountryCollection()
    {
        $collection = $this->getData('country_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('Magento_Directory_Model_Country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }

        return $collection;
    }

    public function getCountryHtmlSelect($defValue=null, $name='country_id', $id='country', $title='Country')
    {
        Magento_Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        if (is_null($defValue)) {
            $defValue = $this->getCountryId();
        }
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = $this->getCountryCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        $html = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName($name)
            ->setId($id)
            ->setTitle(__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();

        Magento_Profiler::stop('TEST: '.__METHOD__);
        return $html;
    }

    public function getRegionCollection()
    {
        $collection = $this->getData('region_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('Magento_Directory_Model_Region')->getResourceCollection()
                ->addCountryFilter($this->getCountryId())
                ->load();

            $this->setData('region_collection', $collection);
        }
        return $collection;
    }


    public function getRegionHtmlSelect()
    {
        Magento_Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE' . Mage::app()->getStore()->getId();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = $this->getRegionCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        $html = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setName('region')
            ->setTitle(__('State/Province'))
            ->setId('state')
            ->setClass('required-entry validate-state')
            ->setValue(intval($this->getRegionId()))
            ->setOptions($options)
            ->getHtml();
        Magento_Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        return $html;
    }

    public function getCountryId()
    {
        $countryId = $this->getData('country_id');
        if (is_null($countryId)) {
            $countryId = $this->_coreData->getDefaultCountry();
        }
        return $countryId;
    }

    public function getRegionsJs()
    {
        Magento_Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        $regionsJs = $this->getData('regions_js');
        if (!$regionsJs) {
            $countryIds = array();
            foreach ($this->getCountryCollection() as $country) {
                $countryIds[] = $country->getCountryId();
            }
            $collection = Mage::getModel('Magento_Directory_Model_Region')->getResourceCollection()
                ->addCountryFilter($countryIds)
                ->load();
            $regions = array();
            foreach ($collection as $region) {
                if (!$region->getRegionId()) {
                    continue;
                }
                $regions[$region->getCountryId()][$region->getRegionId()] = array(
                    'code'=>$region->getCode(),
                    'name'=>$region->getName()
                );
            }
            $regionsJs = $this->_coreData->jsonEncode($regions);
        }
        Magento_Profiler::stop('TEST: '.__METHOD__);
        return $regionsJs;
    }
}
