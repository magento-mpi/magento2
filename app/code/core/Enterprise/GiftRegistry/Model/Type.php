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
        $xmlModel = Mage::getModel('enterprise_giftregistry/attribute_processor');
        $this->setMetaXml($xmlModel->processData($this->getAttribute()));

        parent::_beforeSave();
    }

    /**
     * Perform actions after object load
     *
     * @return Enterprise_GiftRegistry_Model_Type
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();

        $xmlModel = Mage::getModel('enterprise_giftregistry/attribute_processor');
        $this->setAttribute($xmlModel->processXml($this->getMetaXml()));

        return $this;
    }
}
