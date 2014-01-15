<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart operation observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Model;

class Observer extends \Magento\Core\Model\AbstractModel
{
    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_wishlistFactory = $wishlistFactory;
        $this->messageManager = $messageManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get customer wishlist model instance
     *
     * @param   int $customerId
     * @return  \Magento\Wishlist\Model\Wishlist || false
     */
    protected function _getWishlist($customerId)
    {
        if (!$customerId) {
            return false;
        }
        return $this->_wishlistFactory->create()->loadByCustomer($customerId, true);
    }

    /**
     * Check move quote item to wishlist request
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Wishlist\Model\Observer
     */
    public function processCartUpdateBefore($observer)
    {
        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo();
        $productIds = array();

        $wishlist = $this->_getWishlist($cart->getQuote()->getCustomerId());
        if (!$wishlist) {
            return $this;
        }

        /**
         * Collect product ids marked for move to wishlist
         */
        foreach ($data as $itemId => $itemInfo) {
            if (!empty($itemInfo['wishlist'])) {
                if ($item = $cart->getQuote()->getItemById($itemId)) {
                    $productId  = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();

                    if (isset($itemInfo['qty']) && is_numeric($itemInfo['qty'])) {
                        $buyRequest->setQty($itemInfo['qty']);
                    }
                    $wishlist->addNewItem($productId, $buyRequest);

                    $productIds[] = $productId;
                    $cart->getQuote()->removeItem($itemId);
                }
            }
        }

        if (!empty($productIds)) {
            $wishlist->save();
            $this->_wishlistData->calculate();
        }
        return $this;
    }

    public function processAddToCart($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist = $this->_checkoutSession->getSharedWishlist();
        $messages = $this->_checkoutSession->getWishlistPendingMessages();
        $urls = $this->_checkoutSession->getWishlistPendingUrls();
        $wishlistIds = $this->_checkoutSession->getWishlistIds();
        $singleWishlistId = $this->_checkoutSession->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = array($singleWishlistId);
        }

        if (count($wishlistIds) && $request->getParam('wishlist_next')) {
            $wishlistId = array_shift($wishlistIds);

            if ($this->_customerSession->isLoggedIn()) {
                $wishlist = $this->_wishlistFactory->create()
                        ->loadByCustomer($this->_customerSession->getCustomer(), true);
            } else if ($sharedWishlist) {
                $wishlist = $this->_wishlistFactory->create()->loadByCode($sharedWishlist);
            } else {
                return;
            }

            $wishlist->getItemCollection()->load();

            foreach ($wishlist->getItemCollection() as $wishlistItem) {
                if ($wishlistItem->getId() == $wishlistId) {
                    $wishlistItem->delete();
                }
            }
            $this->_checkoutSession->setWishlistIds($wishlistIds);
            $this->_checkoutSession->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && count($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            $this->_checkoutSession->setWishlistPendingUrls($urls);
            $this->_checkoutSession->setWishlistPendingMessages($messages);

            $this->messageManager->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            $this->_checkoutSession->setNoCartRedirect(true);
        }
    }

    /**
     * Customer login processing
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Wishlist\Model\Observer
     */
    public function customerLogin(\Magento\Event\Observer $observer)
    {
        $this->_wishlistData->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Wishlist\Model\Observer
     */
    public function customerLogout(\Magento\Event\Observer $observer)
    {
        $this->_customerSession->setWishlistItemCount(0);

        return $this;
    }
}
