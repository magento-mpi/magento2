<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist block customer items
 */
namespace Magento\Wishlist\Block\Rss;

class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->wishlistHelper = $wishlistHelper;
        $this->rssUrlBuilder = $rssUrlBuilder;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->rssUrlBuilder->getUrl($this->getLinkParams());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Subscribe to Order Status');
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isRssAllowed()
    {
        return (bool)$this->_scopeConfig->getValue(
            'rss/wishlist/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve order status url key
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    protected function getUrlKey($order)
    {
        $data = array(
            'order_id' => $order->getId(),
            'increment_id' => $order->getIncrementId(),
            'customer_id' => $order->getCustomerId()
        );
        return base64_encode(json_encode($data));
    }

    /**
     * @return string
     */
    protected function getLinkParams()
    {
        $params = array();
        $wishlistId = $this->wishlistHelper->getWishlist()->getId();
        $customer = $this->wishlistHelper->getCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = array(
                'type' => 'wishlist',
                'data' => $this->wishlistHelper->urlEncode($key),
                '_secure' => false
            );
        }
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }
        return $params;
    }
}
