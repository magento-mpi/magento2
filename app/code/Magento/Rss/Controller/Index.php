<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Rss\Helper\WishlistRss
     */
    protected $_wishlistHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Rss\Helper\WishlistRss $wishlistHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Rss\Helper\WishlistRss $wishlistHelper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_wishlistHelper = $wishlistHelper;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }
}
