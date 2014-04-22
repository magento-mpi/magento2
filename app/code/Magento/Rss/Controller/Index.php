<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller;

use Magento\Framework\App\Action\NotFoundException;

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

    /**
     * Index action
     *
     * @return void
     * @throws NotFoundException
     */
    public function indexAction()
    {
        if ($this->_scopeConfig->getValue('rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * Display feed not found message
     *
     * @return void
     */
    public function nofeedAction()
    {
        $this->getResponse()->setHeader(
            'HTTP/1.1',
            '404 Not Found'
        )->setHeader(
            'Status',
            '404 File not found'
        )->setHeader(
            'Content-Type',
            'text/plain; charset=UTF-8'
        )->setBody(
            __('There was no RSS feed enabled.')
        );
    }

    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return void
     */
    public function wishlistAction()
    {
        if ($this->_scopeConfig->getValue('rss/wishlist/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $wishlist = $this->_wishlistHelper->getWishlist();
            if ($wishlist && ($wishlist->getVisibility()
                || $this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)
                && $wishlist->getCustomerId() == $this->_wishlistHelper->getCustomer()->getId())
            ) {
                $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
                $this->_view->loadLayout(false);
                $this->_view->renderLayout();
                return;
            }
        }
        $this->nofeedAction();
    }
}
