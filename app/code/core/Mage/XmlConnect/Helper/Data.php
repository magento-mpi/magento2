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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Delimiter for packing store-device pair
     * @var unknown_type
     */
    const APP_DELIMITER = ',';

    /**
     * Create filter object by key
     *
     * @param string $key
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function getFilterByKey($key)
    {
        $filterModelName = 'catalog/layer_filter_attribute';
        switch ($key) {
            case 'price':
                $filterModelName = 'catalog/layer_filter_price';
                break;
            case 'decimal':
                $filterModelName = 'catalog/layer_filter_decimal';
                break;
            case 'category':
                $filterModelName = 'catalog/layer_filter_category';
                break;
            default:
                $filterModelName = 'catalog/layer_filter_attribute';
                break;
        }
        return Mage::getModel($filterModelName);
    }

    /**
     * Exports $this->_getUrl() function to public
     *
     * @param string $route
     * @param array $params
     *
     * @return array
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }


    /**
     * Return country options array
     *
     * @return array
     */
    public function getCountryOptionsArray()
    {
        Varien_Profiler::start('TEST: '.__METHOD__);

        $cacheKey = 'XMLCONNECT_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $options = Mage::getModel('directory/country')
                ->getResourceCollection()
                ->loadByStore()
                ->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        Varien_Profiler::stop('TEST: '.__METHOD__);
        return $options;
    }

    /**
     * Returns list of predefined and supported Devices
     * @return multitype:string
     */
    public function getSupportedDevices()
    {
        $devices = array (
            'iphone' => Mage::helper('xmlconnect')->__('iPhone'),
// not supported yet
//            'ipad' => Mage::helper('xmlconnect')->__('Ipad'),
//            'android' => Mage::helper('xmlconnect')->__('Android'),
        );
        return $devices;
    }

    /**
     * Returns list of predefined and supported Devices
     * @return multitype:string
     */
    public function getStatusOptions()
    {
        $options = array (
            Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS => Mage::helper('xmlconnect')->__('Submitted'),
            Mage_XmlConnect_Model_Application::APP_STATUS_INACTIVE => Mage::helper('xmlconnect')->__('Not Submitted'),
        );
        return $options;
    }


    /**
     * Pack Store and Device to "store-device CS value" like - "1,iphone"
     *
     * @param string $storeId
     * @param string $deviceId
     *
     * @return string
     */
    public function packStoreDevice($storeId = '', $deviceId = '')
    {
        return $storeId . self::APP_DELIMITER . $deviceId;
    }

    /**
     * Unpack store-device CS value  :  "1,iphone" to array
     *
     * @param string $storeDevice
     *
     * @return array
     */
    public function unpackStoreDevice($storeDevice)
    {
        $params = array();
        $arr = explode(self::APP_DELIMITER, $storeDevice);
        if (is_array($arr)) {
            $params['store_id'] = isset($arr[0]) ? $arr[0] : '';
            $params['type'] = isset($arr[1]) ? $arr[1] : '';
        }
        return $params;
    }

    /**
     * Return modifien select option array for stores
     *
     * @return array
     */
    public function getStoreDeviceValuesForForm($filter = false)
    {
        $options = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false);
        $devices = self::getSupportedDevices();
        foreach ($options as $id => $option) {
            if (is_array($option)) {
                if (!empty($option['value']) && is_array($option['value'])) {
                    $storeValueArray = array();
                    $oldStore = current($option['value']);
                    if (isset($oldStore['label']) && isset($oldStore['value'])) {
                        $label = $oldStore['label'];
                        $value = $oldStore['value'];
                        foreach ($devices as $deviceCode => $deviceName) {
                            if (($filter === false) || !((isset($filter[$id]) && ($filter[$id] == $deviceCode)))) {
                                $storeValueArray[] = array (
                                    'label' => $label . ' - ' . $deviceName,
                                    'value' => self::packStoreDevice($value, $deviceCode),
                                );
                            }
                        }
                    }
                    $options[$id]['value'] = $storeValueArray;
                }
            }
        }
        return $options;
    }

    /**
     * Returns array of supported device types as "html select options"
     *
     * @return array
     */
    public function getDeviceValuesForForm()
    {
        $devices = self::getSupportedDevices();
        $options = array();
        if (count($devices) > 1) {
            $options[] = array('value' => '', 'label' => $this->__('Please Select Device Type'));
        }
        foreach ($devices as $type => $label) {
            $options[] = array('value' => $type, 'label' => $label);
        }
        return $options;
    }


    /**
     * Returns array of store & type pairs to create
     *
     * @return array
     */
    public function getStoreDeviceIdsToCreate() {
        $correctIds = array();
        $stores = Mage::app()->getStores();
        $storeIds = array_keys($stores);

        $supportedDevices = Mage::helper('xmlconnect')->getSupportedDevices();
        $existingApplications = Mage::getModel('xmlconnect/application')->getResource()->getExistingStoreDeviceType();

        $filterArray = array();
        if (is_array($existingApplications)) {
            foreach ($existingApplications as $app) {
                if (!isset($filterArray[$app['type']])) {
                    $filterArray[$app['type']] = array();
                }
                $filterArray[$app['type']][$app['store_id']] = 1;
            }
        }

        foreach ($supportedDevices as $deviceType => $deviceName) {
            foreach ($storeIds as $storeId) {
                if (!(isset($filterArray[$deviceType]) && (isset($filterArray[$deviceType][$storeId])))) {
                    $correctIds[$storeId] = $deviceType;
                }
            }
        }
        return $correctIds;
    }

}
