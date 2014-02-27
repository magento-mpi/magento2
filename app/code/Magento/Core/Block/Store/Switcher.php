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
namespace Magento\Core\Block\Store;

class Switcher extends \Magento\View\Element\Template
{
    /**
     * @var array
     */
    protected $_groups = array();

    /**
     * @var array
     */
    protected $_stores = array();

    /**
     * @var bool
     */
    protected $_loaded = false;

    /**
     * Store factory
     *
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * Store group factory
     *
     * @var \Magento\Core\Model\Store\GroupFactory
     */
    protected $_storeGroupFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Store\GroupFactory $storeGroupFactory
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Store\GroupFactory $storeGroupFactory,
        \Magento\Core\Model\StoreFactory $storeFactory,
        array $data = array()
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
        $this->setStores(array());
        $this->setLanguages(array());
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

    /**
     * @return int
     */
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

    /**
     * @return int
     */
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
