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
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Gift registry custom attribute config model
 */
class Enterprise_GiftRegistry_Model_Attribute_Config extends Enterprise_Enterprise_Model_Core_Abstract
{
    protected $_config = null;

    /**
     * Path to attribute groups
     */
    const XML_ATTRIBUTE_GROUPS_PATH = 'prototype/registry/attribute_groups';

    /**
     * Load config from giftregistry.xml files and try to cache it
     *
     * @return Varien_Simplexml_Config
     */
    public function getXmlConfig()
    {
        if (is_null($this->_config)) {
            if ($cachedXml = Mage::app()->loadCache('giftregistry_config')) {
                $xmlConfig = new Varien_Simplexml_Config($cachedXml);
            } else {
                $xmlConfig = new Varien_Simplexml_Config();
                $xmlConfig->loadString('<?xml version="1.0"?><prototype></prototype>');
                Mage::getConfig()->loadModulesConfiguration('giftregistry.xml', $xmlConfig);

                if (Mage::app()->useCache('config')) {
                    Mage::app()->saveCache($xmlConfig->getXmlString(), 'giftregistry_config',
                        array(Mage_Core_Model_Config::CACHE_TAG));
                }
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }

    /**
     * Return array of default options
     *
     * @return array
     */
    protected function _getDefaultOption()
    {
        return array(array(
            'value' => '',
            'label' => Mage::helper('enterprise_giftregistry')->__('-- Please select --'))
        );
    }

    /**
     * Return array of attribute types for using as options
     *
     * @return array
     */
    public function getAttributeTypesOptions()
    {
        return array_merge($this->_getDefaultOption(), $this->getAttributeCustomTypesOptions());
    }

    /**
     * Return array of attribute groups for using as options
     *
     * @return array
     */
    public function getAttributeGroupsOptions()
    {
        $options = $this->_getDefaultOption();
        $groups = $this->getAttributeGroups();

        if (is_array($groups)) {
            foreach ($groups as $code => $group) {
                if ($group['is_custom']) {
                    $options[] = array(
                        'value' => $code,
                        'label' => $group['label']
                    );
                }
            }
        }
        return $options;
    }

    /**
     * Return array of attribute groups
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        if ($groups = $this->getXmlConfig()->getNode(self::XML_ATTRIBUTE_GROUPS_PATH)) {
            return $groups->asCanonicalArray();
        }
    }

    /**
     * Return array of static attribute types for using as options
     *
     * @return array
     */
    public function getStaticTypes()
    {
        $staticTypes = array();
        foreach (array('registry', 'registrant') as $node) {
            if ($node = $this->getXmlConfig()->getNode('prototype/' . $node . '/attributes/static')) {
                $staticTypes = array_merge($staticTypes, $node->asCanonicalArray());
            }
        }
        return $staticTypes;
    }

    /**
     * Return array of codes of static attribute types
     *
     * @return array
     */
    public function getStaticTypesCodes()
    {
        return array_keys($this->getStaticTypes());
    }

    /**
     * Return code of static date attribute type
     *
     * @return null|string
     */
    public function getStaticDateType()
    {
        foreach ($this->getStaticTypes() as $code =>$type) {
            if (isset($type['type']) && $type['type'] == 'date') {
                return $code;
            }
        }
        return null;
    }

    /**
     * Return array of static attribute types for using as options
     *
     * @return array
     */
    public function getAttributeCustomTypesOptions()
    {
        return array(
            array(
                'label' => Mage::helper('enterprise_giftregistry')->__('Text'),
                'value' => 'text'
            ),
            array(
                'label' => Mage::helper('enterprise_giftregistry')->__('Select'),
                'value' => 'select'
            ),
            array(
                'label' => Mage::helper('enterprise_giftregistry')->__('Date'),
                'value' => 'date'
            ),
            array(
                'label' => Mage::helper('enterprise_giftregistry')->__('Region'),
                'value' => 'region'
            ),
            array(
                'label' => Mage::helper('enterprise_giftregistry')->__('Country'),
                'value' => 'country'
            )
        );
    }
}
