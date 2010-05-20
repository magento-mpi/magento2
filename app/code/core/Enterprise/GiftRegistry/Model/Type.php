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
 * Gift registry types processing model
 */
class Enterprise_GiftRegistry_Model_Type extends Enterprise_Enterprise_Model_Core_Abstract
{
    protected $_store = null;
    protected $_storeData = null;

    /**
     * Intialize model
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry/type');
    }

    /**
     * Perform actions before object save.
     */
    protected function _beforeSave()
    {
        if (!$this->getStoreId()) {
            $xmlModel = Mage::getModel('enterprise_giftregistry/attribute_processor');
            $this->setMetaXml($xmlModel->processData($this));
            $this->_cleanupData();
        }

        parent::_beforeSave();
    }

    /**
     * Perform actions after object save.
     */
    protected function _afterSave()
    {
        $this->_getResource()->saveTypeStoreData($this);
        if ($this->getStoreId()) {
            $this->_saveAttributeStoreData();
        }
    }

    /**
     * Perform actions after object load
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();

        $this->assignAttributesStoreData();
        return $this;
    }

    /**
     * Set store id
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function setStoreId($storeId = null)
    {
        $this->_store = Mage::app()->getStore($storeId);
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            $this->setStoreId();
        }

        return $this->_store;
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Save registry type attribute data per store view
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _saveAttributeStoreData()
    {
        if ($groups = $this->getAttributes()) {
            foreach((array)$groups as $attributes) {
                foreach((array)$attributes as $attribute) {
                    $this->_getResource()->saveStoreData($this, $attribute);
                    if (isset($attribute['options']) && is_array($attribute['options'])) {
                        foreach($attribute['options'] as $option) {
                            $optionCode = $option['code'];
                            $option['code'] = $attribute['code'];
                            $this->_getResource()->saveStoreData($this, $option, $optionCode);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Clear object model from data that should be deleted
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    protected function _cleanupData()
    {
        if ($groups = $this->getAttributes()) {
            $attributesToSave = array();
            foreach ((array)$groups as $group => $attributes) {
                foreach ((array)$attributes as $attribute) {
                    if ($attribute['is_deleted']) {
                        $this->_getResource()->deleteAttributeStoreData($this->getId(), $attribute['code']);
                    } else {
                        if (isset($attribute['options']) && is_array($attribute['options'])) {
                            $optionsToSave = array();
                            foreach ($attribute['options'] as $option) {
                                if ($option['is_deleted']) {
                                  $this->_getResource()->deleteAttributeStoreData($this->getId(), $attribute['code'], $option['code']);
                                } else {
                                    $optionsToSave[] = $option;
                                }
                            }
                            $attribute['options'] = $optionsToSave;
                        }
                        $attributesToSave[$group] = $attribute;
                    }
                }
                $this->setAttributes($attributesToSave);
            }
        }
        return $this;
    }

    /**
     * Assign attributes store data
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function assignAttributesStoreData()
    {
        $xmlModel = Mage::getModel('enterprise_giftregistry/attribute_processor');
        $groups = $xmlModel->processXml($this->getMetaXml());
        $storeData = array();

        if (is_array($groups)) {
            foreach ($groups as $group => $attributes) {
                $storeData[$group] = $this->getAttributesStoreData($attributes);
            }
        }
        $this->setAttributes($storeData);
        return $this;
    }

    /**
     * Assign attributes store data
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function getAttributesStoreData($attributes)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $code => $attribute) {
                if ($storeLabel = $this->getAttributeStoreData($code)) {
                    $attributes[$code]['label'] = $storeLabel;
                    $attributes[$code]['default_label'] = $attribute['label'];
                }
                if (isset($attribute['options']) && is_array($attribute['options'])) {
                    $options = array();
                    foreach ($attribute['options'] as $key => $label) {
                        $data = array('code' => $key, 'label' => $label);
                        if ($storeLabel = $this->getAttributeStoreData($code, $key)) {
                            $data['label'] = $storeLabel;
                            $data['default_label'] = $label;
                        }
                        $options[] = $data;
                    }
                    $attributes[$code]['options'] = $options;
                }
            }
        }
        return $attributes;
    }

    /**
     * Retrieve attribute store label
     *
     * @param string $attributeCode
     * @param string $optionCode
     * @return string
     */
    public function getAttributeStoreData($attributeCode, $optionCode = '')
    {
        if ($this->_storeData === null) {
            $this->_storeData = $this->_getResource()->getAttributesStoreData($this);
        }

        if (is_array($this->_storeData)) {
            foreach ($this->_storeData as $item) {
               if ($item['attribute_code'] == $attributeCode && $item['option_code'] == $optionCode) {
                   return $item['label'];
               }
            }
        }
        return '';
    }
}
