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
 * Enterprise search helper
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Search_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Defines text type fields
     * Integer attributes are saved at metadata as text because in fact they are values for
     * options of select type inputs but their values are presented as text aliases
     *
     * @var array
     */
    protected $_textFieldTypes = array(
        'text',
        'varchar',
        'int'
    );

    /**
     * Convert an object to an array
     *
     * @param object $object The object to convert
     * @return array
     */
    public function objectToArray($object)
    {
        if(!is_object($object) && !is_array($object)){
            return $object;
        }
        if(is_object($object)){
            $object = get_object_vars($object);
        }

        return array_map(array($this, 'objectToArray'), $object);
    }

    /**
     * Convert facet results object to an array
     *
     * @param object $object
     * @return array
     */
    public function facetObjectToArray($object)
    {
        if(!is_object($object) && !is_array($object)){
            return $object;
        }

        if(is_object($object)){
            $object = get_object_vars($object);
        }

        $res = array();

        foreach ($object['facet_fields'] as $attr => $val) {
            foreach ($val as $key => $value) {
                $res[$attr][$key] = $value;
            }
        }

        foreach ($object['facet_queries'] as $attr => $val) {
            if (preg_match('/\(categories:(\d+) OR show_in_categories\:\d+\)/', $attr, $matches)) {
                $res['categories'][$matches[1]]    = $val;
            } else {
                $attrArray = explode(':', $attr);
                $res[$attrArray[0]][$attrArray[1]] = $val;
            }
        }
        return $res;
    }

    /**
     * Retrive supported by Solr languages including locale codes (language codes) that are specified in configuration
     * Array(
     *      'language_code1' => 'locale_code',
     *      'language_code2' => Array('locale_code1', 'locale_code2')
     * )
     *
     * @return array
     */
    public function getSolrSupportedLanguages()
    {
        $default = array(
            /**
             * SnowBall filter based
             */
            //Danish
            'da' => 'da_DK',
            //Dutch
            'nl' => 'nl_NL',
            //English
            'en' => array('en_AU', 'en_CA', 'en_NZ', 'en_GB', 'en_US'),
            //Finnish
            'fi' => 'fi_FI',
            //French
            'fr' => array('fr_CA', 'fr_FR'),
            //German
            'de' => array('de_DE','de_DE','de_AT'),
            //Italian
            'it' => array('it_IT','it_CH'),
            //Norwegian
            'nb' => array('nb_NO', 'nn_NO'),
            //Portuguese
            'pt' => array('pt_BR', 'pt_PT'),
            //Romanian
            'ro' => 'ro_RO',
            //Russian
            'ru' => 'ru_RU',
            //Spanish
            'es' => array('es_AR', 'es_CL', 'es_CO', 'es_CR', 'es_ES', 'es_MX', 'es_PA', 'es_PE', 'es_VE'),
            //Swedish
            'sv' => 'sv_SE',
            //Turkish
            'tr' => 'tr_TR',

            /**
             * Lucene class based
             */
            //Czech
            'cs' => 'cs_CZ',
            //Greek
            'el' => 'el_GR',
            //Thai
            'th' => 'th_TH',
            //Chinese
            'zh' => array('zh_CN', 'zh_HK', 'zh_TW'),
            //Japanese
            'ja' => 'ja_JP',
            //Korean
            'ko' => 'ko_KR'
        );

        /**
         * Merging languages that specified manualy
         */
        $node = Mage::getConfig()->getNode('global/enterprise_search/supported_languages/solr');
        if ($node && $node->children()) {
            foreach ($node->children() as $_node) {
                $localeCode = $_node->getName();
                $langCode   = $_node . '';
                if (isset($default[$langCode])) {
                    if (is_array($default[$langCode])) {
                        if (!in_array($localeCode, $default[$langCode])) {
                            $default[$langCode][] = $localeCode;
                        }
                    }
                    elseif ($default[$langCode] != $localeCode) {
                        $default[$langCode] = array($default[$langCode], $localeCode);
                    }
                }
                else {
                    $default[$langCode] = $localeCode;
                }
            }
        }
        return $default;
    }

    /**
     * Retrieve information from Solr search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     */
    public function getSolrConfigData($field, $storeId = null)
    {
        return $this->getSearchConfigData('solr_' . $field, $storeId);
    }

    /**
     * Retrieve information from search engine configuration
     *
     * @param string $field
     * @param int $storeId
     * @return string|int
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/' . $field;
        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Return true if third part search engine used
     *
     */
    public function isThirdPartSearchEngine()
    {
        $engine = $this->getSearchConfigData('engine');
        if ($engine == 'enterprise_search/engine') {
            return true;
        }
        return false;
    }

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param string $localeCode
     *
     * @return false|string
     */
    public function getLanguageCodeByLocaleCode($localeCode)
    {
        $localeCode = (string)$localeCode;
        if (!$localeCode) {
            return false;
        }
        $languages = $this->getSolrSupportedLanguages();
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
     * Retrieve filter array
     *
     * @param Enterprise_Search_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @return array
     */
    public function getSearchParam($collection, $attribute, $value)
    {
        if (empty($value) ||
            (isset($value['from']) && empty($value['from']) &&
            isset($value['to']) && empty($value['to']))) {
            return false;
        }

        $languageCode = $this->getLanguageCodeByLocaleCode(
            Mage::app()->getStore()
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
        $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

        $field = $attribute->getAttributeCode();
        $fieldType = $attribute->getBackendType();
        $frontendInput = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        }
        elseif ($fieldType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        }
        elseif ($fieldType == 'int') {
            $field = 'attr_select_'. $field;
        }
        elseif ($fieldType == 'datetime') {
            $field = 'attr_datetime_'. $field;
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!is_empty_date($val)) {
                        $date = new Zend_Date(
                            $val,
                            Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                        );
                        $val = $date->toString(Zend_Date::ISO_8601) . 'Z';
                    }
                }
            }
            else {
                if (!is_empty_date($value)) {
                    $date = new Zend_Date(
                        $value,
                        Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                    );
                    $value = $date->toString(Zend_Date::ISO_8601) . 'Z';
                }
            }
        }
        elseif (in_array($fieldType, $this->_textFieldTypes)) {
            $field .= $languageSuffix;
        }

        if ($attribute->usesSource()) {
            $attribute->setStoreId(
                Mage::app()->getStore()->getId()
            );

            foreach ($value as &$val) {
                $val = $attribute->getSource()->getOptionText($val);
            }
        }

        return array($field => $value);
    }

    /**
     * Retrive attribute field's name for sorting
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return string
     */
    public function getAttributeSolrFieldName($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        if ($attributeCode == 'score') {
            return $attributeCode;
        }
        $entityType     = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        $attribute      = Mage::getSingleton('eav/config')->getAttribute($entityType, $attributeCode);
        $field          = $attributeCode;
        $backendType    = $attribute->getBackendType();
        $frontendInput  = $attribute->getFrontendInput();

        if ($frontendInput == 'multiselect') {
            $field = 'attr_multi_'. $field;
        } elseif ($backendType == 'int') {
            $field = 'attr_select_'. $field;
        } elseif ($backendType == 'decimal') {
            $field = 'attr_decimal_'. $field;
        } elseif (in_array($backendType, $this->_textFieldTypes)) {
            $languageCode = $this->getLanguageCodeByLocaleCode(
                Mage::app()->getStore()
                ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE));
            $languageSuffix = ($languageCode) ? '_' . $languageCode : '';

            $field .= $languageSuffix;
        }

        return $field;
    }
}
