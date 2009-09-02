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
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Model_Banner extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    const STATUS_ENABLED = 1;
    /**
     * Enter description here...
     *
     */
    const STATUS_DISABLED  = 0;

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_eventPrefix = 'enterprise_banner';

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_eventObject = 'banner';

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_contents = array();

    /**
     * Initialize banner model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_banner/banner');
    }

    /**
     * Enter description here...
     *
     */
    protected function _beforeSave()
    {
//        $banner_name = $this->getBannerName();
//        if (empty($banner_name)) {
//            Mage::throwException(Mage::helper('enterprise_banner')->__('Banner name must be specified'));
//        }
        // content
        return parent::_beforeSave();
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function _afterLoad()
    {
        return parent::_afterLoad();
    }

    /**
     * Retrieve array of sales rules id's for banner
     *
     * array($ruleId => $is_active)
     *
     * @return array
     */
    public function getRelatedSalesRule()
    {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('related_sales_rule');
        if (is_null($array)) {
            $array = $this->getResource()->getRelatedSalesRule($this->getId());
            $this->setData('related_sales_rule', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of catalog rules id's for banner
     *
     * array($ruleId => $is_active)
     *
     * @return array
     */
    public function getRelatedCatalogRule()
    {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('related_catalog_rule');
        if (is_null($array)) {
            $array = $this->getResource()->getRelatedCatalogRule($this->getId());
            $this->setData('related_catalog_rule', $array);
        }
        return $array;
    }


    /**
     * Save banner content after banner save
     *
     * @return Enterprise_Banner_Model_Banner
     */
    protected function _afterSave()
    {
        if ($this->hasStoreContents()) {
            $this->_getResource()->saveStoreContents($this->getId(), $this->getStoreContents(), $this->getStoreContentsNotUse());
        }
        if ($this->hasBannerCatalogRules()) {
            parse_str($this->getBannerCatalogRules(), $rules);
        	$this->_getResource()->saveCatalogRules($this->getId(), array_keys($rules));
        }
        if ($this->hasBannerSalesRules()) {
            parse_str($this->getBannerSalesRules(), $rules);
        	$this->_getResource()->saveSalesRules($this->getId(), array_keys($rules));
        }
        return parent::_afterSave();
    }

    /**
     * Get Rule label for specific store
     *
     * @param   store $store
     * @return  string | false
     */
    public function getStoreContent($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if ($this->hasStoreContents()) {
            $contents = $this->_getData('store_contents');
            if (isset($contents[$storeId])) {
                return $contents[$storeId];
            }
            elseif ($contents[0]) {
                return $contents[0];
            }
            return false;
        }
        elseif (!isset($this->_contents[$storeId])) {
            $this->_contents[$storeId] = $this->_getResource()->getStoreContent($this->getId(), $storeId);
        }
        return $this->_contents[$storeId];
    }

    /**
     * Get all existing banner contents
     *
     * @return array
     */
    public function getStoreContents()
    {
        if (!$this->hasStoreContents()) {
            $contents = $this->_getResource()->getStoreContents($this->getId());
            $this->setStoreContents($contents);
        }
        return $this->_getData('store_contents');
    }

    /**
     * Get all existing banner related catalog rules
     *
     * @return array
     */
    public function getBannerCatalogRules()
    {
        if (!$this->hasBannerCatalogRules()) {
            $rules = array_keys($this->_getResource()->getRelatedCatalogRule($this->getId()));
            $this->setBannerCatalogRules($rules);
        }
        return $this->_getData('banner_catalog_rules');
    }

    /**
     * Get all existing banner related sales rules
     *
     * @return array
     */
    public function getBannerSalesRules()
    {
        if (!$this->hasBannerSalesRules()) {
            $rules = array_keys($this->_getResource()->getRelatedSalesRule($this->getId()));
            $this->setBannerSalesRules($rules);
        }
        return $this->_getData('banner_sales_rules');
    }
}
