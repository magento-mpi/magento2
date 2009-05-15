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
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Collections limiter model
 *
 */
class Enterprise_AdminGws_Model_Collections
{
    /**
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize helper
     *
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('enterprise_admingws');
    }

    /**
     * Limit store views collection
     *
     * @param Mage_Core_Model_Mysql4_Store_Collection $collection
     */
    public function limitStores($collection)
    {
        $collection->addIdFilter(array_merge($this->_helper->getStoreIds(), array(0)));
    }

    /**
     * Limit websites collection
     *
     * @param Mage_Core_Model_Mysql4_Website_Collection $collection
     */
    public function limitWebsites($collection)
    {
        $collection->addIdFilter(array_merge($this->_helper->getRelevantWebsiteIds(), array(0)));
    }

    /**
     * Limit store groups collection
     *
     * @param Mage_Core_Model_Mysql4_Store_Group_Collection $collection
     */
    public function limitStoreGroups($collection)
    {
        $collection->addWebsiteFilter(array_merge($this->_helper->getRelevantWebsiteIds(), array(0)));
    }

    /**
     * Limit a collection by allowed stores without admin
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     */
    public function addStoreFilterNoAdmin($collection)
    {
        $collection->addStoreFilter($this->_helper->getStoreIds(), false);
    }

    /**
     * Add filter by store views to a collection
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     */
    public function addStoreFilter($collection)
    {
        $collection->addStoreFilter($this->_helper->getStoreIds());
    }

    /**
     * Limit products collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     */
    public function limitProducts($collection)
    {
        $collection->addWebsiteFilter($this->_helper->getRelevantWebsiteIds());
    }

    /**
     * Limit customers collection
     *
     * @param Mage_Customer_Model_Entity_Customer_Collection $collection
     */
    public function limitCustomers($collection)
    {
        $collection->addAttributeToFilter('website_id', array('website_id' => array('in' => $this->_helper->getRelevantWebsiteIds())));
    }

    /**
     * Limit reviews collection
     *
     * @param Mage_Review_Model_Mysql4_Review_Collection $collection
     */
    public function limitReviews($collection)
    {
        $collection->addStoreFilter($this->_helper->getStoreIds());
    }

    /**
     * Limit price rules collection
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     */
    public function limitPriceRules($collection)
    {
        $collection->addWebsiteFilter($this->_helper->getWebsiteIds());
    }

    /**
     * Limit online visitor log collection
     *
     * @param Mage_Log_Model_Mysql4_Visitor_Collection $collection
     */
    public function limitOnlineCustomers($collection)
    {
        if ($collection->getIsOnlineFilterUsed()) {
            $collection->addVisitorStoreFilter($this->_helper->getStoreIds());
        }
    }

    /**
     * Limit GCA collection
     *
     * @param Enterprise_GiftCardAccount_Model_Mysql4_Giftcardaccount_Collection $collection
     */
    public function limitGiftCardAccounts($collection)
    {
        $collection->addWebsiteFilter($this->_helper->getWebsiteIds());
    }

    /**
     * Limit Catalog events collection
     *
     * @param Enterprise_CatalogEvent_Model_Mysql4_Event_Collection $collection
     */
    public function limitCatalogEvents($collection)
    {
        $collection->capByCategoryPaths($this->_helper->getAllowedRootCategories());
    }

    /**
     * Limit catalog categories collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection $collection
     */
    public function limitCatalogCategories($collection)
    {
        $collection->addPathsFilter($this->_helper->getAllowedRootCategories());
    }

    /**
     * Limit core URL rewrites
     *
     * @param Mage_Core_Model_Mysql4_Url_Rewrite_Collection $collection
     */
    public function limitCoreUrlRewrites($collection)
    {
        $collection->addStoreFilter($this->_helper->getStoreIds(), false);
    }

    /**
     * Limit ratings collection
     *
     * @param Mage_Rating_Model_Mysql4_Rating_Collection $collection
     */
    public function limitRatings($collection)
    {
        $collection->setStoreFilter($this->_helper->getStoreIds());
    }

    /**
     * Add store_id attribute to filter of EAV-collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     */
    public function addStoreAttributeToFilter($collection)
    {
        $collection->addAttributeToFilter('store_id', array('in' => $this->_helper->getStoreIds()));
    }

    /**
     * Filter checkout agreements collection by allowed stores
     *
     * @param Mage_Checkout_Model_Mysql4_Agreement_Collection $collection
     */
    public function limitCheckoutAgreements($collection)
    {
        $collection->setIsStoreFilterWithAdmin(false)->addStoreFilter($this->_helper->getStoreIds());
    }
}
