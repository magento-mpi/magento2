<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block;

/**
 * Review form block
 */
class ListBlock extends \Magento\View\Element\Template
{
    const XML_PATH_RSS_METHODS = 'rss';

    /**
     * @var array
     */
    protected $_rssFeeds = array();

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Add Link elements to head
     *
     * @return $this
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
     * @return bool|array
     */
    public function getRssFeeds()
    {
        return empty($this->_rssFeeds) ? false : $this->_rssFeeds;
    }

    /**
     * Add new rss feed
     *
     * @param string $url
     * @param string $label
     * @param array $param
     * @param bool $customerGroup
     * @return $this
     */
    public function addRssFeed($url, $label, $param = array(), $customerGroup = false)
    {
        $param = array_merge($param, array('store_id' => $this->getCurrentStoreId()));
        if ($customerGroup) {
            $param = array_merge($param, array('cid' => $this->getCurrentCustomerGroupId()));
        }
        $this->_rssFeeds[] = new \Magento\Object(
            array(
                'url'   => $this->_urlBuilder->getUrl($url, $param),
                'label' => $label
            )
        );
        return $this;
    }

    /**
     * Rest rss feed
     *
     * @return void
     */
    public function resetRssFeed()
    {
        $this->_rssFeeds = array();
    }

    /**
     * Get current store id
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get current customer group id
     *
     * @return int
     */
    public function getCurrentCustomerGroupId()
    {
        return $this->_customerSession->getCustomerGroupId();
    }

    /**
     * Retrieve rss catalog feeds
     *
     * array structure:
     *
     * @return array
     */
    public function getRssCatalogFeeds()
    {
        $this->resetRssFeed();
        $this->categoriesRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * Get rss misc feeds
     *
     * @return array|bool
     */
    public function getRssMiscFeeds()
    {
        $this->resetRssFeed();
        $this->newProductRssFeed();
        $this->specialProductRssFeed();
        $this->salesRuleProductRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * New product rss feed
     *
     * @return void
     */
    public function newProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/new';
        if ((bool)$this->_storeConfig->getValue($path, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            $this->addRssFeed($path, __('New Products'));
        }
    }

    /**
     * Special product rss feed
     *
     * @return void
     */
    public function specialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/special';
        if ((bool)$this->_storeConfig->getValue($path, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            $this->addRssFeed($path, __('Special Products'), array(), true);
        }
    }

    /**
     * Sales rule product rss feed
     *
     * @return void
     */
    public function salesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/salesrule';
        if ((bool)$this->_storeConfig->getValue($path, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            $this->addRssFeed($path, __('Coupons/Discounts'), array(), true);
        }
    }

    /**
     * Categories rss feed
     *
     * @return void
     */
    public function categoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/category';
        if ((bool)$this->_storeConfig->getValue($path, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE)) {
            /** @var $category \Magento\Catalog\Model\Category */
            $category = $this->_categoryFactory->create();
            $treeModel = $category->getTreeModel()->loadNode($this->_storeManager->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = array();
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            /* @var $collection \Magento\Catalog\Model\Resource\Category\Collection */
            $collection = $category->getCollection();
            $collection->addIdFilter($nodeIds)
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active', 1)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                $this->addRssFeed('rss/catalog/category', $category->getName(), array('cid' => $category->getId()));
            }
        }
    }
}
