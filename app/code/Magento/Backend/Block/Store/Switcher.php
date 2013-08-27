<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store switcher block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_Store_Switcher extends Magento_Backend_Block_Template
{
    /**
     * Key in config for store switcher hint
     */
    const XPATH_HINT_KEY = 'store_switcher';

    /**
     * @var array
     */
    protected $_storeIds;

    /**
     * Name of store variable
     *
     * @var string
     */
    protected $_storeVarName = 'store';

    /**
     * Url for store switcher hint
     *
     * @var string
     */
    protected $_hintUrl;

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Magento_Backend::store/switcher.phtml';

    /**
     * Application model
     *
     * @var Magento_Core_Model_App
     */
    protected $_application;

    /**
     * Website factory
     *
     * @var Magento_Core_Model_Website_Factory
     */
    protected $_websiteFactory;

    /**
     * Store Group Factory
     *
     * @var Magento_Core_Model_Store_Group_Factory
     */
    protected $_storeGroupFactory;

    /**
     * Store Factory
     *
     * @var Magento_Core_Model_StoreFactory
     */
    protected $_storeFactory;

    /**
     * Constructor
     *
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param Magento_Core_Model_Website_Factory $websiteFactory
     * @param Magento_Core_Model_Store_Group_Factory $storeGroupFactory
     * @param Magento_Core_Model_StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        Magento_Core_Model_Website_Factory $websiteFactory,
        Magento_Core_Model_Store_Group_Factory $storeGroupFactory,
        Magento_Core_Model_StoreFactory $storeFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_application = $application;
        $this->_websiteFactory = $websiteFactory;
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
    }


    protected function _construct()
    {
        parent::_construct();

        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName(__('All Store Views'));
    }

    /**
     * @return Magento_Core_Model_Resource_Website_Collection
     */
    public function getWebsiteCollection()
    {
        $collection = $this->_websiteFactory->create()->getResourceCollection();

        $websiteIds = $this->getWebsiteIds();
        if (!is_null($websiteIds)) {
            $collection->addIdFilter($this->getWebsiteIds());
        }

        return $collection->load();
    }

    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
        $websites = $this->_application->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            foreach (array_keys($websites) as $websiteId) {
                if (!in_array($websiteId, $websiteIds)) {
                    unset($websites[$websiteId]);
                }
            }
        }
        return $websites;
    }

    /**
     * @param int|Magento_Core_Model_Website $website
     * @return Magento_Core_Model_Resource_Store_Group_Collection
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof Magento_Core_Model_Website) {
            $website = $this->_websiteFactory->create()->load($website);
        }
        return $website->getGroupCollection();
    }

    /**
     * Get store groups for specified website
     *
     * @param Magento_Core_Model_Website|int $website
     * @return array
     */
    public function getStoreGroups($website)
    {
        if (!$website instanceof Magento_Core_Model_Website) {
            $website = $this->_application->getWebsite($website);
        }
        return $website->getGroups();
    }

    /**
     * @param Magento_Core_Model_Store_Group|int $group
     * @return Magento_Core_Model_Resource_Store_Collection
     */
    public function getStoreCollection($group)
    {
        if (!$group instanceof Magento_Core_Model_Store_Group) {
            $group = $this->_storeGroupFactory->create()->load($group);
        }
        $stores = $group->getStoreCollection();
        $_storeIds = $this->getStoreIds();
        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }
        return $stores;
    }

    /**
     * Get store views for specified store group
     *
     * @param Magento_Core_Model_Store_Group|int $group
     * @return array
     */
    public function getStores($group)
    {
        if (!$group instanceof Magento_Core_Model_Store_Group) {
            $group = $this->_application->getGroup($group);
        }
        $stores = $group->getStores();
        if ($storeIds = $this->getStoreIds()) {
            foreach (array_keys($stores) as $storeId) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }
        return $stores;
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_storeVarName => null));
    }

    /**
     * @param string $varName
     * @return Magento_Backend_Block_Store_Switcher
     */
    public function setStoreVarName($varName)
    {
        $this->_storeVarName = $varName;
        return $this;
    }

    /**
     * Get current store
     *
     * @return string
     */
    public function getCurrentStoreName()
    {
        $store = $this->_storeFactory->create();
        $store->load($this->getStoreId());
        if ($store->getId()) {
            return $store->getName();
        } else {
            return $this->getDefaultStoreName();
        }
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam($this->_storeVarName);
    }

    /**
     * @param array $storeIds
     * @return Magento_Backend_Block_Store_Switcher
     */
    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return !$this->_application->isSingleStoreMode();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->isShow()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }

    /**
     * Return url for store switcher hint
     *
     * @return string
     */
    public function getHintUrl()
    {
        if (null === $this->_hintUrl) {
            $this->_hintUrl = $this->helper('Magento_Core_Helper_Hint')->getHintByCode(self::XPATH_HINT_KEY);
        }
        return $this->_hintUrl;
    }

    /**
     * Return store switcher hint html
     *
     * @return string
     */
    public function getHintHtml()
    {
        $html = '';
        $url = $this->getHintUrl();
        if ($url) {
            $html = '<div class="tooltip">'
                . '<span class="help"><a'
                . ' href="'. $this->escapeUrl($url) . '"'
                . ' onclick="this.target=\'_blank\'"'
                . ' title="' . __('What is this?') . '"'
                . ' class="link-store-scope">'
                . __('What is this?')
                . '</a></span>'
                .' </div>';
        }
        return $html;
    }
}
