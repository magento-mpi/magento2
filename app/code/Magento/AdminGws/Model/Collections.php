<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collections limiter model
 */
class Magento_AdminGws_Model_Collections extends Magento_AdminGws_Model_Observer_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager = null;
    
    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_backendAuthSession;

    /**
     * @var Magento_AdminGws_Model_Resource_CollectionsFactory
     */
    protected $_collectionsFactory;

    /**
     * @param Magento_AdminGws_Model_Resource_CollectionsFactory $collectionsFactory
     * @param Magento_Backend_Model_Auth_Session $backendAuthSession
     * @param Magento_AdminGws_Model_Role $role
     * @param Magento_Core_Model_StoreManager $storeManager
     */
    public function __construct(
        Magento_AdminGws_Model_Resource_CollectionsFactory $collectionsFactory,
        Magento_Backend_Model_Auth_Session $backendAuthSession,
        Magento_AdminGws_Model_Role $role,
        Magento_Core_Model_StoreManager $storeManager
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
     * @param Magento_Core_Model_Resource_Store_Collection $collection
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
     * @param Magento_Core_Model_Resource_Website_Collection $collection
     */
    public function limitWebsites($collection)
    {
        $collection->addIdFilter(array_merge($this->_role->getRelevantWebsiteIds(), array(0)));
        $collection->addFilterByGroupIds(array_merge($this->_role->getStoreGroupIds(), array(0)));
    }

    /**
     * Limit store groups collection
     *
     * @param Magento_Core_Model_Resource_Store_Group_Collection $collection
     */
    public function limitStoreGroups($collection)
    {
        $collection->addFieldToFilter('group_id',
            array('in'=>array_merge($this->_role->getStoreGroupIds(), array(0)))
        );
    }

    /**
     * Limit a collection by allowed stores without admin
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    public function addStoreFilterNoAdmin($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds(), false);
    }

    /**
     * Add filter by store views to a collection
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    public function addStoreFilter($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit products collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     */
    public function limitProducts($collection)
    {
        $relevantWebsiteIds = $this->_role->getRelevantWebsiteIds();
        $websiteIds = array();
        $filters    = $collection->getLimitationFilters();

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
     * @param Magento_Customer_Model_Resource_Customer_Collection $collection
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
     * @param Magento_Review_Model_Resource_Review_Collection $collection
     */
    public function limitReviews($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit product reviews collection
     *
     * @param Magento_Review_Model_Resource_Review_Product_Collection $collection
     */
    public function limitProductReviews($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Limit online visitor log collection
     *
     * @param Magento_Log_Model_Resource_Visitor_Collection $collection
     */
    public function limitOnlineCustomers($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit GCA collection
     *
     * @param Magento_GiftCardAccount_Model_Resource_Giftcardaccount_Collection $collection
     */
    public function limitGiftCardAccounts($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit Reward Points history collection
     *
     * @param Magento_Reward_Model_Resource_Reward_History_Collection $collection
     */
    public function limitRewardHistoryWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit Reward Points balance collection
     *
     * @param Magento_Reward_Model_Resource_Reward_Collection $collection
     */
    public function limitRewardBalanceWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit store credit collection
     *
     * @param Magento_CustomerBalance_Model_Resource_Balance_Collection $collection
     */
    public function limitStoreCredits($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit store credit collection
     *
     * @param Magento_CustomerBalance_Model_Resource_Balance_History_Collection $collection
     */
    public function limitStoreCreditsHistory($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }


    /**
     * Limit Catalog events collection
     *
     * @param Magento_CatalogEvent_Model_Resource_Event_Collection $collection
     */
    public function limitCatalogEvents($collection)
    {
        $collection->capByCategoryPaths($this->_role->getAllowedRootCategories());
    }

    /**
     * Limit catalog categories collection
     *
     * @param Magento_Catalog_Model_Resource_Category_Collection $collection
     */
    public function limitCatalogCategories($collection)
    {
        $collection->addPathsFilter($this->_role->getAllowedRootCategories());
    }

    /**
     * Limit core URL rewrites
     *
     * @param Magento_Core_Model_Resource_Url_Rewrite_Collection $collection
     */
    public function limitCoreUrlRewrites($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds(), false);
    }

    /**
     * Limit ratings collection
     *
     * @param Magento_Rating_Model_Resource_Rating_Collection $collection
     */
    public function limitRatings($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Add store_id attribute to filter of EAV-collection
     *
     * @param Magento_Eav_Model_Entity_Collection_Abstract $collection
     */
    public function addStoreAttributeToFilter($collection)
    {
        $collection->addAttributeToFilter('store_id', array('in' => $this->_role->getStoreIds()));
    }

    /**
     * Filter checkout agreements collection by allowed stores
     *
     * @param Magento_Checkout_Model_Resource_Agreement_Collection $collection
     */
    public function limitCheckoutAgreements($collection)
    {
        $collection->setIsStoreFilterWithAdmin(false)->addStoreFilter($this->_role->getStoreIds());
    }

    /**
     * Filter admin roles collection by allowed stores
     *
     * @param Magento_User_Model_Resource_Role_Collection $collection
     */
    public function limitAdminPermissionRoles($collection)
    {
        $limited = $this->_collectionsFactory->create()
            ->getRolesOutsideLimitedScope(
                $this->_role->getIsAll(),
                $this->_role->getWebsiteIds(),
                $this->_role->getStoreGroupIds()
            );

        $collection->addFieldToFilter('role_id', array('nin' => $limited));
    }

    /**
     * Filter admin users collection by allowed stores
     *
     * @param Magento_User_Model_Resource_User_Collection $collection
     */
    public function limitAdminPermissionUsers($collection)
    {
        $limited = $this->_collectionsFactory->create()
            ->getUsersOutsideLimitedScope(
                $this->_role->getIsAll(),
                $this->_role->getWebsiteIds(),
                $this->_role->getStoreGroupIds()
            );
        $collection->addFieldToFilter('user_id', array('nin' => $limited));
    }

    /**
     * Filter sales collection by allowed stores
     *
     * @param Magento_Event_Observer $observer
     */
    public function addSalesSaleCollectionStoreFilter($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        $this->addStoreFilter($collection);
    }

    /**
     * Apply store filter on collection used in new order's rss
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Collections
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
     * @return Magento_AdminGws_Model_Collections
     */
    protected function _initRssAdminRole()
    {
        /* @var $adminUser Magento_User_Model_User */
        $adminUser = $this->_backendAuthSession->getUser();
        if ($adminUser) {
            $this->_role->setAdminRole($adminUser->getRole());
        }
        return $this;
    }

    /**
     * Apply websites filter on collection used in notify stock rss
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Collections
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
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Collections
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
     * @param  Magento_Reports_Model_Resource_Product_Collection $collection
     */
    public function limitProductReports($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds(), $this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit GiftRegistry Entity collection
     *
     * @param Magento_GiftRegistry_Model_Resource_Entity_Collection $collection
     */
    public function limitGiftRegistryEntityWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }

    /**
     * Limit bestsellers collection
     *
     * @param Magento_Sales_Model_Resource_Report_Bestsellers_Collection $collection
     */
    public function limitBestsellersCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }

    /**
     * Limit most viewed collection
     *
     * @param Magento_Reports_Model_Resource_Report_Product_Viewed_Collection $collection
     */
    public function limitMostViewedCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }

    /**
     * Limit Automated Email Marketing Reminder Rules collection
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    public function limitRuleEntityCollection($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }





    /**
     * Limit customer segment collection
     *
     * @deprecated after 1.12.0.0 use $this->limitRuleEntityCollection() for any rule based collection
     *
     * @param Magento_CustomerSegment_Model_Resource_Segment_Collection $collection
     */
    public function limitCustomerSegments($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }

    /**
     * Limit price rules collection
     *
     * @deprecated after 1.12.0.0 use $this->limitRuleEntityCollection() for any rule based collection
     *
     * @param Magento_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    public function limitPriceRules($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }
}
