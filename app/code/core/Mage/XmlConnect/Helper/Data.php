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
}
