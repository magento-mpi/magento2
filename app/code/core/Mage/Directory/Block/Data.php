<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory data block
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Block_Data extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Core_Model_Cache_Type_Config
     */
    protected $_configCacheType;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session_Abstract $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session_Abstract $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $data);
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
            $collection = Mage::getModel('Mage_Directory_Model_Country')->getResourceCollection()
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
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('Mage_Directory_Helper_Data')->__($title))
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
            $collection = Mage::getModel('Mage_Directory_Model_Region')->getResourceCollection()
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
        $html = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
            ->setName('region')
            ->setTitle(Mage::helper('Mage_Directory_Helper_Data')->__('State/Province'))
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
            $countryId = Mage::helper('Mage_Core_Helper_Data')->getDefaultCountry();
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
            $collection = Mage::getModel('Mage_Directory_Model_Region')->getResourceCollection()
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
            $regionsJs = Mage::helper('Mage_Core_Helper_Data')->jsonEncode($regions);
        }
        Magento_Profiler::stop('TEST: '.__METHOD__);
        return $regionsJs;
    }
}
