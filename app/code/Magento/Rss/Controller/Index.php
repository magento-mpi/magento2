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

use Magento\App\Action\NotFoundException;

class Index extends \Magento\App\Action\Action
{
    /**
     * Current wishlist
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    /**
     * Current customer
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Store\Config $storeConfig
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Store\Config $storeConfig
    ) {
        $this->_storeConfig = $storeConfig;
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
        if ($this->_storeConfig->getConfig('rss/config/active')) {
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
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found')
            ->setHeader('Status', '404 File not found')
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setBody(__('There was no RSS feed enabled.'))
        ;
    }

    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return void
     */
    public function wishlistAction()
    {
        if ($this->_storeConfig->getConfig('rss/wishlist/active')) {
            $wishlist = $this->_getWishlist();
            if ($wishlist && ($wishlist->getVisibility()
                || $this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($this)
                    && $wishlist->getCustomerId() == $this->_getCustomer()->getId())
            ) {
                $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
                $this->_view->loadLayout(false);
                $this->_view->renderLayout();
                return;
            }
        }
        $this->nofeedAction();
    }

    /**
     * Retrieve Wishlist model
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = $this->_objectManager->create('Magento\Wishlist\Model\Wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if ($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
            $params = $this->_objectManager->get('Magento\Core\Helper\Data')
                ->urlDecode($this->getRequest()->getParam('data'));
            $data = explode(',', $params);
            $customerId    = abs(intval($data[0]));
            if ($customerId
                && ($customerId == $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId()) ) {
                $this->_customer->load($customerId);
            }
        }
        return $this->_customer;
    }
}
