<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Solr search engine abstract adapter
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Search_Model_Adapter_Solr_Abstract extends Enterprise_Search_Model_Adapter_Abstract
{
    /**
     * Define ping status 
     *
     * @var float | bool
     */
    protected $_ping = null;

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dateFormats = array();



    /**
     * Retrive Solr server status
     *
     * @return float Actual time taken to ping the server, FALSE if timeout or HTTP error status occurs
     */
    public function ping()
    {
        if (is_null($this->_ping)){
            try {
                $this->_ping = $this->_client->ping();
            }
            catch (Exception $e){
                $this->_ping = false;
            }
        }

        return $this->_ping;
    }

    /**
     * Retrieve language code by specified locale code if this locale is supported by Solr
     *
     * @param string $localeCode
     *
     * @return false|string
     */
    protected function _getLanguageCodeByLocaleCode($localeCode)
    {
        $localeCode = (string)$localeCode;
        if (!$localeCode) {
            return false;
        }
        $languages = Mage::helper('enterprise_search')->getSolrSupportedLanguages();
        foreach ($languages as $code => $locales) {
            if (is_array($locales)) {
                if (in_array($localeCode, $locales)) {
                    return $code;
                }
            }
            elseif ($localeCode == $locales) {
                return $code;
            }
        }

        return false;
    }

    /**
     * Retrieve date value in solr format (ISO 8601) with Z
     * Example: 1995-12-31T23:59:59Z
     *
     * @param int $storeId
     * @param string $date
     *
     * @return string
     */
    protected function _getSolrDate($storeId, $date = null)
    {
        if (!isset($this->_dateFormats[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj  = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dateFormats[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (is_empty_date($date)) {
            return null;
        }

        list($dateObj, $localeDateFormat) = $this->_dateFormats[$storeId];
        $dateObj->setDate($date, $localeDateFormat);

        return $dateObj->toString(Zend_Date::ISO_8601) . 'Z';
    }
}
