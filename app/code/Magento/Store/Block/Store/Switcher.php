<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Store switcher block
 */
namespace Magento\Store\Block\Store;

class Switcher extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $_groups = [];

    /**
     * @var array
     */
    protected $_stores = [];

    /**
     * @var bool
     */
    protected $_loaded = false;

    /**
     * Store factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * Store group factory
     *
     * @var \Magento\Store\Model\GroupFactory
     */
    protected $_storeGroupFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\GroupFactory $storeGroupFactory
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\GroupFactory $storeGroupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        array $data = []
    ) {
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_loadData();
        $this->setStores([]);
        $this->setLanguages([]);
        return parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _loadData()
    {
        if ($this->_loaded) {
            return $this;
        }

        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $storeCollection = $this->_storeFactory->create()->getCollection()->addWebsiteFilter($websiteId);
        $groupCollection = $this->_storeGroupFactory->create()->getCollection()->addWebsiteFilter($websiteId);
        foreach ($groupCollection as $group) {
            $this->_groups[$group->getId()] = $group;
        }
        foreach ($storeCollection as $store) {
            if (!$store->getIsActive()) {
                continue;
            }
            $store->setLocaleCode($this->_scopeConfig->getValue(
                \Magento\Core\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            ));
            $this->_stores[$store->getGroupId()][$store->getId()] = $store;
        }

        $this->_loaded = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreCount()
    {
        $stores = [];
        $localeCode = $this->_scopeConfig->getValue(
            \Magento\Core\Helper\Data::XML_PATH_DEFAULT_LOCALE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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

    /**
     * @return int
     */
    public function getLanguageCount()
    {
        $groupId = $this->_storeManager->getStore()->getGroupId();
        if (!isset($this->_stores[$groupId])) {
            $this->setLanguages([]);
            return 0;
        }
        $this->setLanguages($this->_stores[$groupId]);
        return count($this->getLanguages());
    }

    /**
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return string
     */
    public function getCurrentStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
