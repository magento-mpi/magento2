<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Adminhtml;

use Magento\Framework\App\Rss\DataProviderInterface;

/**
 * Class Rss
 * @package Magento\Catalog\Block\Adminhtml\Rss
 */
class Rss extends \Magento\Backend\Block\AbstractBlock implements DataProviderInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Rss\Product\Review
     */
    protected $rssModel;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\Rss $rssModel,
        array $data = array()
    ) {
        $this->storeManager = $storeManager;
        $this->rssModel = $rssModel;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getRssData()
    {
        $newUrl = $this->getUrl('rss/catalog/review', array('_secure' => true, '_nosecret' => true));
        $title = __('Pending product review(s)');

        $data = array('title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8');

        foreach ($this->rssModel->getProductCollection() as $item) {
            if ($item->getStoreId()) {
                $this->_urlBuilder->setScope($item->getStoreId());
            }

            $url = $this->getUrl('catalog/product/view', array('id' => $item->getId()));
            $reviewUrl = $this->getUrl('review/product/edit/', array(
                'id' => $item->getReviewId(),
                '_secure' => true,
                '_nosecret' => true
            ));

            $storeName = $this->storeManager->getStore($item->getStoreId())->getName();
            $description = '<p>' . __('Product: <a href="%1" target="_blank">%2</a> <br/>', $url, $item->getName())
                . __('Summary of review: %1 <br/>', $item['title']) . __('Review: %1 <br/>', $item->getDetail())
                . __('Store: %1 <br/>', $storeName)
                . __('Click <a href="%1">here</a> to view the review.', $reviewUrl)
                . '</p>';

            $data['entries'][] = array(
                'title' => __('Product: "%1" reviewed by: %2', $item->getName(), $item->getNickname()),
                'link' => 'test',
                'description' => $description
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheLifetime()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        return array();
    }
}
