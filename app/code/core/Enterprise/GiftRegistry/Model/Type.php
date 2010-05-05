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

    /**
     * Intialize model
     */
    protected function _construct()
    {
        $this->_init('enterprise_giftregistry/type');
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
        if ($this->getId() && $this->getStoreId()) {
            $this->_getResource()->saveTypeStoreData($this);
            $this->_saveAttributeStoreData();
        }
    }

    /**
     * Save registry type attribute data per store view
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _saveAttributeStoreData()
    {
        if ($attributes = $this->getAttributes()) {
            if (is_array($attributes)) {
                foreach($attributes as $attribute) {
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
        if ($attributes = $this->getAttributes()) {
            $attributesToSave = array();
            foreach ($attributes as $attribute) {
                if ($attribute['is_deleted']) {
                    $this->_getResource()->deleteStoreData($this->getId(), $attribute['code']);
                } else {
                    if (isset($attribute['options']) && is_array($attribute['options'])) {
                        $optionsToSave = array();
                        foreach ($attribute['options'] as $option) {
                            if ($option['is_deleted']) {
                                $this->_getResource()->deleteOptionStoreData($this->getId(), $attribute['code'], $option['code']);
                            } else {
                                $optionsToSave[] = $option;
                            }
                        }
                        $attribute['options'] = $optionsToSave;
                    }
                    $attributesToSave[] = $attribute;
                }
            }
            $this->setAttributes($attributesToSave);
        }
        return $this;
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
     * Assign attributes store data
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function assignAttributesStoreData()
    {
        $xmlModel = Mage::getModel('enterprise_giftregistry/attribute_processor');
        $attributes = $xmlModel->processXml($this->getMetaXml());

        if (is_array($attributes)) {
            foreach ($attributes as $code => $attribute) {
                if ($storeLabel = $this->getAttributeStoreLabel($code)) {
                    $attributes[$code]['label'] = $storeLabel;
                    $attributes[$code]['default_label'] = $attribute['label'];
                }
                if (isset($attribute['options']) && is_array($attribute['options'])) {
                    $options = array();
                    foreach ($attribute['options'] as $key => $label) {
                        $data = array('code' => $key, 'label' => $label);
                        if ($storeLabel = $this->getOptionStoreLabel($code, $key)) {
                            $data['label'] = $storeLabel;
                            $data['default_label'] = $attribute['label'];
                        }
                        $options[] = $data;
                    }
                    $attributes[$code]['options'] = $options;
                }
            }
        }
        $this->setAttributes($attributes);
        return $this;
    }

    /**
     * Retrieve attribute store label
     *
     * @param string $code
     * @return null|string
     */
    public function getAttributeStoreLabel($code)
    {
        return $this->_getResource()->getAttributeStoreLabel($this, $code);
    }

    /**
     * Retrieve attribute option store label
     *
     * @param string $option
     * @return Enterprise_GiftRegistry_Model_Type
     */
    public function getOptionStoreLabel($code, $option)
    {
        return $this->_getResource()->getAttributeStoreOptions($this, $code, $option);
    }
}
