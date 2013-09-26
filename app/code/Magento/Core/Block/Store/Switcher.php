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

    /**
     * Store factory
     *
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * Store group factory
     *
     * @var Magento_Core_Model_Store_GroupFactory
     */
    protected $_storeGroupFactory;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Store_GroupFactory $storeGroupFactory
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_GroupFactory $storeGroupFactory,
        Magento_Core_Model_StoreFactory $storeFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

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

        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $storeCollection = $this->_storeFactory->create()
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        $groupCollection = $this->_storeGroupFactory->create()
            ->getCollection()
            ->addWebsiteFilter($websiteId);
        foreach ($groupCollection as $group) {
            $this->_groups[$group->getId()] = $group;
        }
        foreach ($storeCollection as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            $store->setLocaleCode($this->_storeConfig->getConfig('general/locale/code', $store->getId()));
            $this->_stores[$store->getGroupId()][$store->getId()] = $store;
        }

        $this->_loaded = true;

        return $this;
    }

    public function getStoreCount()
    {
        $stores = array();
        $localeCode = $this->_storeConfig->getConfig('general/locale/code');
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
        $groupId = $this->_storeManager->getStore()->getGroupId();
        if (!isset($this->_stores[$groupId])) {
            $this->setLanguages(array());
            return 0;
        }
        $this->setLanguages($this->_stores[$groupId]);
        return count($this->getLanguages());
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getCurrentStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
