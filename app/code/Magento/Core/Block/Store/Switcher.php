<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store switcher block
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Block_Store_Switcher extends Magento_Core_Block_Template
{
    protected $_groups = array();
    protected $_stores = array();
    protected $_loaded = false;

    protected function _construct()
    {
        $this->_loadData();
        $this->setStores(array());
        $this->setLanguages(array());
        return parent::_construct();
    }

    protected function _loadData()
    {
        if ($this->_loaded) {
            return $this;
        }

        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $storeCollection = Mage::getModel('Magento_Core_Model_Store')
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        $groupCollection = Mage::getModel('Magento_Core_Model_Store_Group')
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        foreach ($groupCollection as $group) {
            $this->_groups[$group->getId()] = $group;
        }
        foreach ($storeCollection as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            $store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));
            $this->_stores[$store->getGroupId()][$store->getId()] = $store;
        }

        $this->_loaded = true;

        return $this;
    }

    public function getStoreCount()
    {
        $stores = array();
        $localeCode = Mage::getStoreConfig('general/locale/code');
        foreach ($this->_groups as $group) {
            if (!isset($this->_stores[$group->getId()])) {
                continue;
            }
            $useStore = false;
            foreach ($this->_stores[$group->getId()] as $store) {
                if ($store->getLocaleCode() == $localeCode) {
                    $useStore = true;
                    $stores[] = $store;
                }
            }
            if (!$useStore && isset($this->_stores[$group->getId()][$group->getDefaultStoreId()])) {
                $stores[] = $this->_stores[$group->getId()][$group->getDefaultStoreId()];
            }
        }

        $this->setStores($stores);
        return count($this->getStores());
    }

    public function getLanguageCount()
    {
        $groupId = Mage::app()->getStore()->getGroupId();
        if (!isset($this->_stores[$groupId])) {
            $this->setLanguages(array());
            return 0;
        }
        $this->setLanguages($this->_stores[$groupId]);
        return count($this->getLanguages());
    }

    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getCurrentStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }
}
