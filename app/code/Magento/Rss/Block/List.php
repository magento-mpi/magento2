<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_List extends Magento_Core_Block_Template
{
    const XML_PATH_RSS_METHODS = 'rss';

    protected $_rssFeeds = array();


    /**
     * Add Link elements to head
     *
     * @return Magento_Rss_Block_List
     */
    protected function _prepareLayout()
    {
        $head   = $this->getLayout()->getBlock('head');
        $feeds  = $this->getRssMiscFeeds();
        if ($head && !empty($feeds)) {
            foreach ($feeds as $feed) {
                $head->addRss($feed['label'], $feed['url']);
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve rss feeds
     *
     * @return array
     */
    public function getRssFeeds()
    {
        return empty($this->_rssFeeds) ? false : $this->_rssFeeds;
    }

    /**
     * Add new rss feed
     *
     * @param   string $url
     * @param   string $label
     * @return  Magento_Core_Helper_Abstract
     */
    public function addRssFeed($url, $label, $param = array(), $customerGroup=false)
    {
        $param = array_merge($param, array('store_id' => $this->getCurrentStoreId()));
        if ($customerGroup) {
            $param = array_merge($param, array('cid' => $this->getCurrentCustomerGroupId()));
        }

        $this->_rssFeeds[] = new Magento_Object(
            array(
                'url'   => Mage::getUrl($url, $param),
                'label' => $label
            )
        );
        return $this;
    }

    public function resetRssFeed()
    {
        $this->_rssFeeds=array();
    }

    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    public function getCurrentCustomerGroupId()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
    }

    /**
     * Retrieve rss catalog feeds
     *
     * array structure:
     *
     * @return  array
     */
    public function getRssCatalogFeeds()
    {
        $this->resetRssFeed();
        $this->CategoriesRssFeed();
        return $this->getRssFeeds();
    }

    public function getRssMiscFeeds()
    {
        $this->resetRssFeed();
        $this->NewProductRssFeed();
        $this->SpecialProductRssFeed();
        $this->SalesRuleProductRssFeed();
        return $this->getRssFeeds();
    }

    /*
    public function getCatalogRssUrl($code)
    {
        $store_id = Mage::app()->getStore()->getId();
        $param = array('store_id' => $store_id);
        $custGroup = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerGroupId();
        if ($custGroup) {
            $param = array_merge($param, array('cid' => $custGroup));
        }

        return Mage::getUrl('rss/catalog/'.$code, $param);
    }
    */

    public function NewProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/new';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, __('New Products'));
        }
    }

    public function SpecialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/special';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, __('Special Products'),array(),true);
        }
    }

    public function SalesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/salesrule';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, __('Coupons/Discounts'),array(),true);
        }
    }

    public function CategoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/category';
        if((bool)Mage::getStoreConfig($path)){
            $category = Mage::getModel('Magento_Catalog_Model_Category');

            /* @var $collection Magento_Catalog_Model_Resource_Category_Collection */
            $treeModel = $category->getTreeModel()->loadNode(Mage::app()->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = array();
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            $collection = $category->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active',1)
                ->addIdFilter($nodeIds)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                $this->addRssFeed('rss/catalog/category', $category->getName(),array('cid'=>$category->getId()));
            }
        }
    }
}
