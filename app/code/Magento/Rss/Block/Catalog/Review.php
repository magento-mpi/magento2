<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Block\Catalog;

/**
 * Review form block
 */
class Review extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Framework\Model\Resource\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Framework\Model\Resource\Iterator $resourceIterator
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Framework\Model\Resource\Iterator $resourceIterator,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
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
        $newUrl = $this->getUrl('rss/catalog/review', array('_secure' => true, '_nosecret' => true));
        $title = __('Pending product review(s)');

        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(
            array('title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8')
        );

        /** @var $reviewModel \Magento\Review\Model\Review */
        $reviewModel = $this->_reviewFactory->create();
        $collection = $reviewModel->getProductCollection()->addStatusFilter(
            $reviewModel->getPendingStatus()
        )->addAttributeToSelect(
            'name',
            'inner'
        )->setDateOrder();

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
     * @return void
     */
    public function addReviewItemXmlCallback($args)
    {
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $row = $args['row'];

        $productUrl = $this->_urlBuilder->setScope(
            $row['store_id']
        )->getUrl(
            'catalog/product/view',
            array('id' => $row['entity_id'])
        );
        $reviewUrl = $this->getUrl(
            'review/product/edit/',
            array('id' => $row['review_id'], '_secure' => true, '_nosecret' => true)
        );
        $storeName = $this->_storeManager->getStore($row['store_id'])->getName();
        $description = '<p>' . __(
            'Product: <a href="%1">%2</a> <br/>',
            $productUrl,
            $row['name']
        ) . __(
            'Summary of review: %1 <br/>',
            $row['title']
        ) . __(
            'Review: %1 <br/>',
            $row['detail']
        ) . __(
            'Store: %1 <br/>',
            $storeName
        ) . __(
            'Click <a href="%1">here</a> to view the review.',
            $reviewUrl
        ) . '</p>';
        $rssObj->_addEntry(
            array(
                'title' => __('Product: "%1" review By: %2', $row['name'], $row['nickname']),
                'link' => 'test',
                'description' => $description
            )
        );
    }
}
