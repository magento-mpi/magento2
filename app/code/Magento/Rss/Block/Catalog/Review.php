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
 */
class Magento_Rss_Block_Catalog_Review extends Magento_Core_Block_Abstract
{
    /**
     * Rss data
     *
     * @var Magento_Rss_Helper_Data
     */
    protected $_rssData = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_resourceIterator;

    /**
     * @var Magento_Review_Model_ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Rss_Helper_Data $rssData
     * @param Magento_Core_Block_Context $context
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Core_Model_Resource_Iterator $resourceIterator
     * @param Magento_Review_Model_ReviewFactory $reviewFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Rss_Helper_Data $rssData,
        Magento_Core_Block_Context $context,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Core_Model_Resource_Iterator $resourceIterator,
        Magento_Review_Model_ReviewFactory $reviewFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_rssData = $rssData;
        $this->_rssFactory = $rssFactory;
        $this->_resourceIterator = $resourceIterator;
        $this->_reviewFactory = $reviewFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Render XML response
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/review');
        $title = __('Pending product review(s)');
        $this->_rssData->disableFlat();

        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(array(
            'title' => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ));

        /** @var $reviewModel Magento_Review_Model_Review */
        $reviewModel = $this->_reviewFactory->create();
        $collection = $reviewModel->getProductCollection()
            ->addStatusFilter($reviewModel->getPendingStatus())
            ->addAttributeToSelect('name', 'inner')
            ->setDateOrder();

        $this->_eventManager->dispatch('rss_catalog_review_collection_select', array('collection' => $collection));

        $this->_resourceIterator->walk(
            $collection->getSelect(),
            array(array($this, 'addReviewItemXmlCallback')),
            array('rssObj' => $rssObj, 'reviewModel' => $reviewModel)
        );
        return $rssObj->createRssXml();
    }

    /**
     * Format single RSS element
     *
     * @param array $args
     * @return null
     */
    public function addReviewItemXmlCallback($args)
    {
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $args['rssObj'];
        $row = $args['row'];

        $store = $this->_storeManager->getStore($row['store_id']);
        $productUrl = $store->getUrl('catalog/product/view', array('id' => $row['entity_id']));
        $reviewUrl = $this->_adminhtmlData->getUrl(
            'adminhtml/catalog_product_review/edit/',
            array('id' => $row['review_id'], '_secure' => true, '_nosecret' => true)
        );
        $storeName = $store->getName();
        $description = '<p>'
             . __('Product: <a href="%1">%2</a> <br/>', $productUrl, $row['name'])
             . __('Summary of review: %1 <br/>', $row['title'])
             . __('Review: %1 <br/>', $row['detail'])
             . __('Store: %1 <br/>', $storeName )
             . __('Click <a href="%1">here</a> to view the review.', $reviewUrl)
             . '</p>';
        $rssObj->_addEntry(array(
            'title'       => __('Product: "%1" review By: %2', $row['name'], $row['nickname']),
            'link'        => 'test',
            'description' => $description,
        ));
    }
}
