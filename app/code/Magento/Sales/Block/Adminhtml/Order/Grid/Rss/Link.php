<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Grid\Rss;

/**
 * Class Link
 * @package Magento\Sales\Block\Adminhtml\Order\Grid\Rss
 */
class Link extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/grid/rss/link.phtml';

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
     * @param \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Rss\UrlBuilderInterface $rssUrlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = array()
    ) {
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
        return __('New Order RSS');
    }

    /**
     * Check whether status notification is allowed
     *
     * @return bool
     */
    public function isRssAllowed()
    {
        return (bool) $this->config->getValue('rss/order/new', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    protected function getLinkParams()
    {
        return array('type' => 'new_order');
    }
}
