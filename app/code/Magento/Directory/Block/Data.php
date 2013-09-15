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
 * \Directory data block
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Block;

class Data extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\Core\Model\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
            $collection = \Mage::getModel('Magento\Directory\Model\Country')->getResourceCollection()
                ->loadByStore();
            $this->setData('country_collection', $collection);
        }

        return $collection;
    }

    public function getCountryHtmlSelect($defValue=null, $name='country_id', $id='country', $title='Country')
    {
        \Magento\Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        if (is_null($defValue)) {
            $defValue = $this->getCountryId();
        }
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . \Mage::app()->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = $this->getCountryCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        $html = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
            ->setName($name)
            ->setId($id)
            ->setTitle(__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();

        \Magento\Profiler::stop('TEST: '.__METHOD__);
        return $html;
    }

    public function getRegionCollection()
    {
        $collection = $this->getData('region_collection');
        if (is_null($collection)) {
            $collection = \Mage::getModel('Magento\Directory\Model\Region')->getResourceCollection()
                ->addCountryFilter($this->getCountryId())
                ->load();

            $this->setData('region_collection', $collection);
        }
        return $collection;
    }


    public function getRegionHtmlSelect()
    {
        \Magento\Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        $cacheKey = 'DIRECTORY_REGION_SELECT_STORE' . \Mage::app()->getStore()->getId();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = $this->getRegionCollection()->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        $html = $this->getLayout()->createBlock('Magento\Core\Block\Html\Select')
            ->setName('region')
            ->setTitle(__('State/Province'))
            ->setId('state')
            ->setClass('required-entry validate-state')
            ->setValue(intval($this->getRegionId()))
            ->setOptions($options)
            ->getHtml();
        \Magento\Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
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
        \Magento\Profiler::start('TEST: '.__METHOD__, array('group' => 'TEST', 'method' => __METHOD__));
        $regionsJs = $this->getData('regions_js');
        if (!$regionsJs) {
            $countryIds = array();
            foreach ($this->getCountryCollection() as $country) {
                $countryIds[] = $country->getCountryId();
            }
            $collection = \Mage::getModel('Magento\Directory\Model\Region')->getResourceCollection()
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
        \Magento\Profiler::stop('TEST: '.__METHOD__);
        return $regionsJs;
    }
}
