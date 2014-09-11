<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

/**
 * Collections limiter model
 */
class Collections extends \Magento\AdminGws\Model\Observer\AbstractObserver implements CallbackProcessorInterface
{
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @var \Magento\AdminGws\Model\Resource\CollectionsFactory
     */
    protected $_collectionsFactory;

    /**
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\AdminGws\Model\Resource\CollectionsFactory $collectionsFactory
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\AdminGws\Model\Resource\CollectionsFactory $collectionsFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_collectionsFactory = $collectionsFactory;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_storeManager = $storeManager;
        parent::__construct($role);
    }

    /**
     * Limit store views collection. Adding limitation depending
     * on allowed group ids for user.
     *
     * @param \Magento\Store\Model\Resource\Store\Collection $collection
     * @return void
     */
    public function limitStores($collection)
    {
        // Changed from filter by store id bc of case when
        // user creating new store view for allowed store group
        $collection->addGroupFilter(array_merge($this->_role->getStoreGroupIds(), array(0)));
    }

    /**
     * Limit websites collection
     *
     * @param \Magento\Store\Model\Resource\Website\Collection $collection
     * @return void
     */
    public function limitWebsites($collection)
    {
        $collection->addIdFilter(array_merge($this->_role->getRelevantWebsiteIds(), array(0)));
        $collection->addFilterByGroupIds(array_merge($this->_role->getStoreGroupIds(), array(0)));
    }

    /**
     * Limit store groups collection
     *
     * @param \Magento\Store\Model\Resource\Group\Collection $collection
     * @return void
     */
    public function limitStoreGroups($collection)
    {
        $collection->addFieldToFilter(
            'group_id',
            array('in' => array_merge($this->_role->getStoreGroupIds(), array(0)))
        );
    }

    /**
     * Limit a collection by allowed stores without admin
     *
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection
     * @return void
     */
    public function addStoreFilterNoAdmin($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds(), false);
    }

    /**
     * Add filter by store views to a collection
     *
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection
     * @return void
     */
    public function addStoreFilter($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit products collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return void
     */
    public function limitProducts($collection)
    {
        $relevantWebsiteIds = $this->_role->getRelevantWebsiteIds();
        $websiteIds = array();
        $filters = $collection->getLimitationFilters();

        if (isset($filters['website_ids'])) {
            $websiteIds = (array)$filters['website_ids'];
        }
        if (isset($filters['store_id'])) {
            $websiteIds[] = $this->_storeManager->getStore($filters['store_id'])->getWebsiteId();
        }

        if (count($websiteIds)) {
            $collection->addWebsiteFilter(array_intersect($websiteIds, $relevantWebsiteIds));
        } else {
            $collection->addWebsiteFilter($relevantWebsiteIds);
        }
    }

    /**
     * Limit customers collection
     *
     * @param \Magento\Customer\Model\Resource\Customer\Collection $collection
     * @return void
     */
    public function limitCustomers($collection)
    {
        $collection->addAttributeToFilter(
            'website_id',
            array('website_id' => array('in' => $this->_role->getRelevantWebsiteIds()))
        );
    }

    /**
     * Limit reviews collection
     *
     * @param \Magento\Review\Model\Resource\Review\Collection $collection
     * @return void
     */
    public function limitReviews($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit product reviews collection
     *
     * @param \Magento\Review\Model\Resource\Review\Product\Collection $collection
     * @return void
     */
    public function limitProductReviews($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit online visitor log collection
     *
     * @param \Magento\Log\Model\Resource\Visitor\Collection $collection
     * @return void
     */
    public function limitOnlineCustomers($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit GCA collection
     *
     * @param \Magento\GiftCardAccount\Model\Resource\Giftcardaccount\Collection $collection
     * @return void
     */
    public function limitGiftCardAccounts($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit Reward Points history collection
     *
     * @param \Magento\Reward\Model\Resource\Reward\History\Collection $collection
     * @return void
     */
    public function limitRewardHistoryWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit Reward Points balance collection
     *
     * @param \Magento\Reward\Model\Resource\Reward\Collection $collection
     * @return void
     */
    public function limitRewardBalanceWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit store credit collection
     *
     * @param \Magento\CustomerBalance\Model\Resource\Balance\Collection $collection
     * @return void
     */
    public function limitStoreCredits($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit store credit collection
     *
     * @param \Magento\CustomerBalance\Model\Resource\Balance\History\Collection $collection
     * @return void
     */
    public function limitStoreCreditsHistory($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit Catalog events collection
     *
     * @param \Magento\CatalogEvent\Model\Resource\Event\Collection $collection
     * @return void
     */
    public function limitCatalogEvents($collection)
    {
        $collection->capByCategoryPaths($this->_role->getAllowedRootCategories());
    }

    /**
     * Limit catalog categories collection
     *
     * @param \Magento\Catalog\Model\Resource\Category\Collection $collection
     * @return void
     */
    public function limitCatalogCategories($collection)
    {
        $collection->addPathsFilter($this->_role->getAllowedRootCategories());
    }

    /**
     * Limit core URL rewrites
     *
     * @param \Magento\UrlRewrite\Model\Resource\UrlRewriteCollection $collection
     * @return void
     */
    public function limitUrlRewrites($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds(), false);
    }

    /**
     * Limit ratings collection
     *
     * @param \Magento\Review\Model\Resource\Rating\Collection $collection
     * @return void
     */
    public function limitRatings($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Add store_id attribute to filter of EAV-collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @return void
     */
    public function addStoreAttributeToFilter($collection)
    {
        $collection->addAttributeToFilter('store_id', array('in' => $this->_role->getStoreIds()));
    }

    /**
     * Filter checkout agreements collection by allowed stores
     *
     * @param \Magento\CheckoutAgreements\Model\Resource\Agreement\Collection $collection
     * @return void
     */
    public function limitCheckoutAgreements($collection)
    {
        $collection->setIsStoreFilterWithAdmin(false)->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Filter admin roles collection by allowed stores
     *
     * @param \Magento\Authorization\Model\Resource\Role\Collection $collection
     * @return void
     */
    public function limitAdminPermissionRoles($collection)
    {
        $limited = $this->_collectionsFactory->create()->getRolesOutsideLimitedScope(
            $this->_role->getIsAll(),
            $this->_role->getWebsiteIds(),
            $this->_role->getStoreGroupIds()
        );

        $collection->addFieldToFilter('role_id', array('nin' => $limited));
    }

    /**
     * Filter admin users collection by allowed stores
     *
     * @param \Magento\User\Model\Resource\User\Collection $collection
     * @return void
     */
    public function limitAdminPermissionUsers($collection)
    {
        $limited = $this->_collectionsFactory->create()->getUsersOutsideLimitedScope(
            $this->_role->getIsAll(),
            $this->_role->getWebsiteIds(),
            $this->_role->getStoreGroupIds()
        );
        $collection->addFieldToFilter('user_id', array('nin' => $limited));
    }

    /**
     * Filter sales collection by allowed stores
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addSalesSaleCollectionStoreFilter($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        $this->addStoreFilter($collection);
    }

    /**
     * Apply store filter on collection used in new order's rss
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function rssOrderNewCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->addStoreAttributeToFilter($collection);
        return $this;
    }

    /**
     * Sets admin role. This is vital for limitProducts(), otherwise getRelevantWebsiteIds() returns an empty array.
     *
     * @return $this
     */
    protected function _initRssAdminRole()
    {
        /* @var $adminUser \Magento\User\Model\User */
        $adminUser = $this->_backendAuthSession->getUser();
        if ($adminUser) {
            $this->_role->setAdminRole($adminUser->getRole());
        }
        return $this;
    }

    /**
     * Apply websites filter on collection used in notify stock rss
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function rssCatalogNotifyStockCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_initRssAdminRole()->limitProducts($collection);
        return $this;
    }

    /**
     * Apply websites filter on collection used in review rss
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function rssCatalogReviewCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_initRssAdminRole()->limitProducts($collection);
        return $this;
    }

    /**
     * Limit product reports
     *
     * @param  \Magento\Reports\Model\Resource\Product\Collection $collection
     * @return void
     */
    public function limitProductReports($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds(), $this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit GiftRegistry Entity collection
     *
     * @param \Magento\GiftRegistry\Model\Resource\Entity\Collection $collection
     * @return void
     */
    public function limitGiftRegistryEntityWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit bestsellers collection
     *
     * @param \Magento\Sales\Model\Resource\Report\Bestsellers\Collection $collection
     * @return void
     */
    public function limitBestsellersCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }

    /**
     * Limit most viewed collection
     *
     * @param \Magento\Reports\Model\Resource\Report\Product\Viewed\Collection $collection
     * @return void
     */
    public function limitMostViewedCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }

    /**
     * Limit Automated Email Marketing Reminder Rules collection
     *
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection
     * @return void
     */
    public function limitRuleEntityCollection($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit customer segment collection
     *
     * @param \Magento\CustomerSegment\Model\Resource\Segment\Collection $collection
     * @return void
     * @deprecated after 1.12.0.0 use $this->limitRuleEntityCollection() for any rule based collection
     */
    public function limitCustomerSegments($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }

    /**
     * Limit price rules collection
     *
     * @param \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection $collection
     * @return void
     * @deprecated after 1.12.0.0 use $this->limitRuleEntityCollection() for any rule based collection
     */
    public function limitPriceRules($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }
}
