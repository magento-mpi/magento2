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
 */
namespace Magento\Backend\Block\Store;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Switcher extends \Magento\Backend\Block\Template
{
    /**
     * URL for store switcher hint
     */
    const HINT_URL = 'http://www.magentocommerce.com/knowledge-base/entry/understanding-store-scopes';

    /**
     * Name of website variable
     *
     * @var string
     */
    protected $_defaultWebsiteVarName = 'website';

    /**
     * Name of store group variable
     *
     * @var string
     */
    protected $_defaultStoreGroupVarName = 'group';

    /**
     * Name of store variable
     *
     * @var string
     */
    protected $_defaultStoreVarName = 'store';

    /**
     * @var array
     */
    protected $_storeIds;

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
     * Website factory
     *
     * @var \Magento\Core\Model\Website\Factory
     */
    protected $_websiteFactory;

    /**
     * Store Group Factory
     *
     * @var \Magento\Core\Model\Store\Group\Factory
     */
    protected $_storeGroupFactory;

    /**
     * Store Factory
     *
     * @var \Magento\Core\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Website\Factory $websiteFactory
     * @param \Magento\Core\Model\Store\Group\Factory $storeGroupFactory
     * @param \Magento\Core\Model\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Website\Factory $websiteFactory,
        \Magento\Core\Model\Store\Group\Factory $storeGroupFactory,
        \Magento\Core\Model\StoreFactory $storeFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_websiteFactory = $websiteFactory;
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setUseConfirm(true);
        $this->setUseAjax(true);

        $this->setShowManageStoresLink(0);

        if (!$this->hasData('switch_websites')) {
            $this->setSwitchWebsites(false);
        }
        if (!$this->hasData('switch_store_groups')) {
            $this->setSwitchStoreGroups(false);
        }
        if (!$this->hasData('switch_store_views')) {
            $this->setSwitchStoreViews(true);
        }
        $this->setDefaultSelectionName(__('All Store Views'));
    }

    /**
     * @return \Magento\Core\Model\Resource\Website\Collection
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
     * @return \Magento\Core\Model\Website[]
     */
    public function getWebsites()
    {
        $websites = $this->_storeManager->getWebsites();
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
     * Check if can switch to websites
     *
     * @return bool
     */
    public function isWebsiteSwitchEnabled()
    {
        return (bool)$this->getData('switch_websites');
    }

    /**
     * @param string $varName
     * @return $this
     */
    public function setWebsiteVarName($varName)
    {
        $this->setData('website_var_name', $varName);
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsiteVarName()
    {
        if ($this->hasData('website_var_name')) {
            return (string)$this->getData('website_var_name');
        } else {
            return (string)$this->_defaultWebsiteVarName;
        }
    }

    /**
     * @param \Magento\Core\Model\Website $website
     * @return bool
     */
    public function isWebsiteSelected(\Magento\Core\Model\Website $website)
    {
        return $this->getWebsiteId() === $website->getId() && $this->getStoreId() === null;
    }

    /**
     * @return int|null
     */
    public function getWebsiteId()
    {
        if (!$this->hasData('website_id')) {
            $this->setData('website_id', $this->getRequest()->getParam($this->getWebsiteVarName()));
        }
        return $this->getData('website_id');
    }

    /**
     * @param int|\Magento\Core\Model\Website $website
     * @return \Magento\Core\Model\Resource\Store\Group\Collection
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof \Magento\Core\Model\Website) {
            $website = $this->_websiteFactory->create()->load($website);
        }
        return $website->getGroupCollection();
    }

    /**
     * Get store groups for specified website
     *
     * @param \Magento\Core\Model\Website|int $website
     * @return array
     */
    public function getStoreGroups($website)
    {
        if (!$website instanceof \Magento\Core\Model\Website) {
            $website = $this->_storeManager->getWebsite($website);
        }
        return $website->getGroups();
    }

    /**
     * Check if can switch to store group
     *
     * @return bool
     */
    public function isStoreGroupSwitchEnabled()
    {
        return (bool)$this->getData('switch_store_groups');
    }

    /**
     * @param string $varName
     * @return $this
     */
    public function setStoreGroupVarName($varName)
    {
        $this->setData('store_group_var_name', $varName);
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreGroupVarName()
    {
        if ($this->hasData('store_group_var_name')) {
            return (string)$this->getData('store_group_var_name');
        } else {
            return (string)$this->_defaultStoreGroupVarName;
        }
    }

    /**
     * @param \Magento\Core\Model\Store\Group $group
     * @return bool
     */
    public function isStoreGroupSelected(\Magento\Core\Model\Store\Group $group)
    {
        return $this->getStoreGroupId() === $group->getId() && $this->getStoreGroupId() === null;
    }

    /**
     * @return int|null
     */
    public function getStoreGroupId()
    {
        if (!$this->hasData('store_group_id')) {
            $this->setData('store_group_id', $this->getRequest()->getParam($this->getStoreGroupVarName()));
        }
        return $this->getData('store_group_id');
    }

    /**
     * @param \Magento\Core\Model\Store\Group|int $group
     * @return \Magento\Core\Model\Resource\Store\Collection
     */
    public function getStoreCollection($group)
    {
        if (!$group instanceof \Magento\Core\Model\Store\Group) {
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
     * @param \Magento\Core\Model\Store\Group|int $group
     * @return \Magento\Core\Model\Store[]
     */
    public function getStores($group)
    {
        if (!$group instanceof \Magento\Core\Model\Store\Group) {
            $group = $this->_storeManager->getGroup($group);
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
     * @return int|null
     */
    public function getStoreId()
    {
        if (!$this->hasData('store_id')) {
            $this->setData('store_id', $this->getRequest()->getParam($this->getStoreVarName()));
        }
        return $this->getData('store_id');
    }

    /**
     * @param \Magento\Core\Model\Store $store
     * @return bool
     */
    public function isStoreSelected(\Magento\Core\Model\Store $store)
    {
        return $this->getStoreId() !== null && (int)$this->getStoreId() === (int)$store->getId();
    }

    /**
     * Check if can switch to store views
     *
     * @return bool
     */
    public function isStoreSwitchEnabled()
    {
        return (bool)$this->getData('switch_store_views');
    }

    /**
     * @param string $varName
     * @return $this
     */
    public function setStoreVarName($varName)
    {
        $this->setData('store_var_name', $varName);
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getStoreVarName()
    {
        if ($this->hasData('store_var_name')) {
            return (string)$this->getData('store_var_name');
        } else {
            return (string)$this->_defaultStoreVarName;
        }
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl(
            '*/*/*',
            [
                '_current' => true,
                $this->getStoreVarName() => null,
                $this->getStoreGroupVarName() => null,
                $this->getWebsiteVarName() => null,
            ]
        );
    }

    /**
     * @return bool
     */
    public function hasScopeSelected()
    {
        return $this->getStoreId() !== null || $this->getStoreGroupId() !== null || $this->getWebsiteId() !== null;
    }

    /**
     * Get current selection name
     *
     * @return string
     */
    public function getCurrentSelectionName()
    {
        if (!($name = $this->getCurrentStoreName())) {
            if (!($name = $this->getCurrentStoreGroupName())) {
                if (!($name = $this->getCurrentWebsiteName())) {
                    $name = $this->getDefaultSelectionName();
                }
            }
        }
        return $name;
    }

    /**
     * Get current website name
     *
     * @return string
     */
    public function getCurrentWebsiteName()
    {
        if ($this->getWebsiteId() !== null) {
            $website = $this->_websiteFactory->create();
            $website->load($this->getWebsiteId());
            if ($website->getId()) {
                return $website->getName();
            }
        }
    }

    /**
     * Get current store group name
     *
     * @return string
     */
    public function getCurrentStoreGroupName()
    {
        if ($this->getStoreGroupId() !== null) {
            $group = $this->_storeGroupFactory->create();
            $group->load($this->getStoreGroupId());
            if ($group->getId()) {
                return $group->getName();
            }
        }
    }

    /**
     * Get current store view name
     *
     * @return string
     */
    public function getCurrentStoreName()
    {
        if ($this->getStoreId() !== null) {
            $store = $this->_storeFactory->create();
            $store->load($this->getStoreId());
            if ($store->getId()) {
                return $store->getName();
            }
        }
    }

    /**
     * @param array $storeIds
     * @return $this
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
        return !$this->_storeManager->isSingleStoreMode();
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
        return self::HINT_URL;
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
            $html = '<div class="tooltip">' . '<span class="help"><a' . ' href="' . $this->escapeUrl(
                $url
            ) . '"' . ' onclick="this.target=\'_blank\'"' . ' title="' . __(
                'What is this?'
            ) . '"' . ' class="link-store-scope"><span>' . __(
                'What is this?'
            ) . '</span></a></span>' . ' </div>';
        }
        return $html;
    }
}
