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

class Enterprise_Search_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
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
            if (preg_match('/\(categories:(\d)+ OR show_in_categories\:\d+\)/', $attr, $matches)) {
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
        $path = 'catalog/search/solr_'.$field;
        return Mage::getStoreConfig($path, $storeId);
    }
}
