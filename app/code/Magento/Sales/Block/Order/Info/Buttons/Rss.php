<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Order\Info\Buttons;

/**
 * Block of links in Order view page
 */
class Rss extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/info/buttons/rss.phtml';

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\App\Rss\UrlBuilderInterface
     */
    protected $rssUrlBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = array()
    ) {
        $this->orderFactory = $orderFactory;
        $this->rssUrlBuilder = $rssUrlBuilder;
        $this->config = $scopeConfig;
        parent::__construct($context, $data);
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
        return (bool) $this->config->getValue('rss/order/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
        $order = $this->orderFactory->create()->load($this->_request->getParam('order_id'));
        return array(
            'type' => 'order_status',
            '_secure' => true,
            '_query' => array('data' => $this->getUrlKey($order))
        );
    }
}
