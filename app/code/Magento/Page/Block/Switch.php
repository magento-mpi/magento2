<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store and language switcher block
 *
 * @category   Magento
 * @package    Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Switch extends Magento_Core_Block_Template
{
    protected $_storeInUrl;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    public function getCurrentWebsiteId()
    {
        return Mage::app()->getStore()->getWebsiteId();
    }

    public function getCurrentGroupId()
    {
        return Mage::app()->getStore()->getGroupId();
    }

    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getRawGroups()
    {
        if (!$this->hasData('raw_groups')) {
            $websiteGroups = Mage::app()->getWebsite()->getGroups();

            $groups = array();
            foreach ($websiteGroups as $group) {
                $groups[$group->getId()] = $group;
            }
            $this->setData('raw_groups', $groups);
        }
        return $this->getData('raw_groups');
    }

    public function getRawStores()
    {
        if (!$this->hasData('raw_stores')) {
            $websiteStores = Mage::app()->getWebsite()->getStores();
            $stores = array();
            foreach ($websiteStores as $store) {
                /* @var $store Magento_Core_Model_Store */
                if (!$store->getIsActive()) {
                    continue;
                }
                $store->setLocaleCode(Mage::getStoreConfig('general/locale/code', $store->getId()));

                $params = array(
                    '_query' => array()
                );
                if (!$this->isStoreInUrl()) {
                    $params['_query']['___store'] = $store->getCode();
                }
                $baseUrl = $store->getUrl('', $params);

                $store->setHomeUrl($baseUrl);
                $stores[$store->getGroupId()][$store->getId()] = $store;
            }
            $this->setData('raw_stores', $stores);
        }
        return $this->getData('raw_stores');
    }

    /**
     * Retrieve list of store groups with default urls set
     *
     * @return array
     */
    public function getGroups()
    {
        if (!$this->hasData('groups')) {
            $rawGroups = $this->getRawGroups();
            $rawStores = $this->getRawStores();

            $groups = array();
            $localeCode = Mage::getStoreConfig('general/locale/code');
            foreach ($rawGroups as $group) {
                /* @var $group Magento_Core_Model_Store_Group */
                if (!isset($rawStores[$group->getId()])) {
                    continue;
                }
                if ($group->getId() == $this->getCurrentGroupId()) {
                    $groups[] = $group;
                    continue;
                }

                $store = $group->getDefaultStoreByLocale($localeCode);

                if ($store) {
                    $group->setHomeUrl($store->getHomeUrl());
                    $groups[] = $group;
                }
            }
            $this->setData('groups', $groups);
        }
        return $this->getData('groups');
    }

    public function getStores()
    {
        if (!$this->getData('stores')) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $stores = array();
            } else {
                $stores = $rawStores[$groupId];
            }
            $this->setData('stores', $stores);
        }
        return $this->getData('stores');
    }

    public function getCurrentStoreCode()
    {
        return Mage::app()->getStore()->getCode();
    }

    public function isStoreInUrl()
    {
        if (is_null($this->_storeInUrl)) {
            $this->_storeInUrl = $this->_storeManager->getStore()->isUseStoreInUrl();
        }
        return $this->_storeInUrl;
    }
}
