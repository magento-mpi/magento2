<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Controller;

/**
 * Multiple wishlist frontend search controller
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Index extends \Magento\Wishlist\Controller\Index
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Wishlist session
     *
     * @var \Magento\Wishlist\Model\Session
     */
    protected $_wishlistSession;

    /**
     * Item factory
     *
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Wishlist collection factory
     *
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $_wishlistCollectionFactory;

    /**
     * Construct
     *
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\Core\Model\Url|\Magento\Core\Model\UrlInterface $url
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Core\Model\Session\Generic $wishlistSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\Core\Model\Url $url,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Core\Model\Session\Generic $wishlistSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_wishlistSession = $wishlistSession;
        $this->_customerSession = $customerSession;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct($context, $coreRegistry, $wishlistConfig, $url);
    }

    /**
     * Check if multiple wishlist is enabled on current store before all other actions
     *
     * @return \Magento\MultipleWishlist\Controller\Index
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $protectedActions = array(
            'createwishlist', 'editwishlist', 'deletewishlist', 'copyitems', 'moveitem', 'moveitems'
        );
        if (!$this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled()
            && in_array($action, $protectedActions)
        ) {
            $this->norouteAction();
        }

        return $this;
    }

    /**
     * Retrieve customer session
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    /**
     * Add item to wishlist
     * Create new wishlist if wishlist params (name, visibility) are provided
     */
    public function addAction()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $name = $this->getRequest()->getParam('name');
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        if ($name !== null) {
            try {
                $wishlist = $this->_editWishlist($customerId, $name, $visibility);
                $this->_getSession()->addSuccess(
                    __('Wish List "%1" was saved.', $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName()))
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    __('Something went wrong creating the wish list.')
                );
            }
        }
        parent::addAction();
    }

    /**
     * Display customer wishlist
     */
    public function indexAction()
    {
        /* @var $helper \Magento\MultipleWishlist\Helper\Data */
        $helper = $this->_objectManager->get('Magento\MultipleWishlist\Helper\Data');
        if (!$helper->isMultipleEnabled() ) {
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId && $wishlistId != $helper->getDefaultWishlist()->getId() ) {
                $this->_redirectUrl($helper->getListUrl());
            }
        }
        parent::indexAction();
    }

    /**
     * Create new customer wishlist
     */
    public function createwishlistAction()
    {
        $this->_forward('editwishlist');
    }

    /**
     * Edit wishlist
     *
     * @param int $customerId
     * @param string $wishlistName
     * @param bool $visibility
     * @param int $wishlistId
     * @return \Magento\Wishlist\Model\Wishlist
     * @throws \Magento\Core\Exception
     */
    protected function _editWishlist($customerId, $wishlistName, $visibility = false, $wishlistId = null)
    {
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_wishlistFactory->create();

        if (!$customerId) {
            throw new \Magento\Core\Exception(__('Log in to edit wish lists.'));
        }
        if (!strlen($wishlistName)) {
            throw new \Magento\Core\Exception(__('Provide wish list name'));
        }
        if ($wishlistId){
            $wishlist->load($wishlistId);
            if ($wishlist->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                throw new \Magento\Core\Exception(
                    __('The wish list is not assigned to your account and cannot be edited.')
                );
            }
        } else {
            /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistCollection */
            $wishlistCollection = $this->_wishlistCollectionFactory->create();
            $wishlistCollection->filterByCustomerId($customerId);
            $limit = $this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->getWishlistLimit();
            if ($this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isWishlistLimitReached($wishlistCollection)) {
                throw new \Magento\Core\Exception(__('Only %1 wish lists can be created.', $limit));
            }
            $wishlist->setCustomerId($customerId);
        }
        $wishlist->setName($wishlistName)
            ->setVisibility($visibility)
            ->generateSharingCode()
            ->save();
        return $wishlist;
    }

    /**
     * Edit wishlist properties
     *
     * @return \Magento\Core\Controller\Varien\Action|\Zend_Controller_Response_Abstract
     */
    public function editwishlistAction()
    {
        $customerId = $this->_getSession()->getCustomerId();
        $wishlistName = $this->getRequest()->getParam('name');
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = null;
        try {
            $wishlist = $this->_editWishlist($customerId, $wishlistName, $visibility, $wishlistId);

            $this->_getSession()->addSuccess(
                __('Wish List "%1" was saved.', $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName()))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException(
                $e,
                __('Something went wrong creating the wish list.')
            );
        }

        if (!$wishlist || !$wishlist->getId()) {
            $this->_getSession()->addError('Could not create wishlist');
        }

        if ($this->getRequest()->isAjax()) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $params = array();
            if (!$wishlist->getId()) {
                $params = array('redirect' => $this->_url->getUrl('*/*'));
            } else {
                $params = array('wishlist_id' => $wishlist->getId());
            }
            return $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($params));
        } else {
            if (!$wishlist || !$wishlist->getId()) {
                return $this->_redirect('*/*');
            } else {
                $this->_redirect('wishlist/index/index', array('wishlist_id' => $wishlist->getId()));
            }
        }
    }

    /**
     * Delete wishlist by id
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function deletewishlistAction()
    {
        try {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                return $this->norouteAction();
            }
            if ($this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isWishlistDefault($wishlist)) {
                throw new \Magento\Core\Exception(__('The default wish list cannot be deleted.'));
            }
            $wishlist->delete();
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $this->_wishlistSession->addSuccess(
                __('Wish list "%1" has been deleted.', $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName()))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong deleting the wish list.');
            $this->_getSession()->addException($e, $message);
        }
    }

    /**
     * Build wishlist product name list string
     *
     * @param array $items
     * @return string
     */
    protected function _joinProductNames($items)
    {
        $names = array();
        foreach ($items as $item) {
            $names[] = '"' . $item->getProduct()->getName() . '"';
        }
        return join(', ', $names);
    }

    /**
     * Copy item to given wishlist
     *
     * @param \Magento\Wishlist\Model\Item $item
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param int $qty
     * @throws \InvalidArgumentException|\DomainException
     */
    protected function _copyItem(\Magento\Wishlist\Model\Item $item, \Magento\Wishlist\Model\Wishlist $wishlist, $qty = null)
    {
        if (!$item->getId()) {
            throw new \InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new \DomainException();
        }
        $buyRequest = $item->getBuyRequest();
        if ($qty) {
            $buyRequest->setQty($qty);
        }
        $wishlist->addNewItem($item->getProduct(), $buyRequest);
        $this->_eventManager->dispatch(
            'wishlist_add_product',
            array(
                'wishlist'  => $wishlist,
                'product'   => $item->getProduct(),
                'item'      => $item
            )
        );
    }

    /**
     * Copy wishlist item to given wishlist
     *
     * @return void
     */
    public function copyitemAction()
    {
        $session = $this->_getSession();
        $requestParams = $this->getRequest()->getParams();
        if ($session->getBeforeWishlistRequest()) {
            $requestParams = $session->getBeforeWishlistRequest();
            $session->unsBeforeWishlistRequest();
        }

        $wishlist = $this->_getWishlist(isset($requestParams['wishlist_id']) ? $requestParams['wishlist_id'] : null);
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = isset($requestParams['item_id']) ? $requestParams['item_id'] : null;
        $qty = isset($requestParams['qty']) ? $requestParams['qty'] : null;
        if ($itemId) {
            $productName = '';
            try {
                /* @var \Magento\Wishlist\Model\Item $item */
                $item = $this->_itemFactory->create();
                $item->loadWithOptions($itemId);

                $wishlistName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName());
                $productName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($item->getProduct()->getName());

                $this->_copyItem($item, $wishlist, $qty);
                $this->_getSession()->addSuccess(
                    __('"%1" was copied to %2.', $productName, $wishlistName)
                );
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            } catch (\InvalidArgumentException $e) {
                $this->_getSession->addError(
                    __('The item was not found.')
                );
            } catch (\DomainException $e) {
                $this->_getSession()->addError(
                    __('"%1" is already present in %2.', $productName, $wishlistName)
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                if ($productName) {
                    $message = __('We could not copy "%1".', $productName);
                } else {
                    $message = __('We could not copy the wish list item.');
                }
                $this->_getSession()->addError($message);
            }
        }
        $wishlist->save();
        if ($this->_getSession()->hasBeforeWishlistUrl())
        {
            $this->_redirectUrl($this->_getSession()->getBeforeWishlistUrl());
            $this->_getSession()->unsBeforeWishlistUrl();
        } else {
            $this->_redirectReferer();
        }
    }

    /**
     * Copy wishlist items to given wishlist
     *
     * @return void
     */
    public function copyitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $notFound = array();
        $alreadyPresent = array();
        $failed = array();
        $copied = array();
        if (count($itemIds)) {
            $qtys = $this->getRequest()->getParam('qty', array());
            foreach ($itemIds as $id => $value) {
                try {
                    /* @var \Magento\Wishlist\Model\Item $item */
                    $item = $this->_itemFactory->create();
                    $item->loadWithOptions($id);

                    $this->_copyItem($item, $wishlist, isset($qtys[$id]) ? $qtys[$id] : null);
                    $copied[$id] = $item;
                } catch (\InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (\DomainException $e) {
                    $alreadyPresent[$id] = $item;
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }
        $wishlistName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName());

        $wishlist->save();

        if (count($notFound)) {
            $this->_getSession()->addError(
                __('%1 items were not found.', count($notFound))
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                __('We could not copy %1 items.', count($failed))
            );
        }

        if (count($alreadyPresent)) {
            $names = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->_getSession()->addError(
                __('%1 items are already present in %2: %3.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($copied)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($this->_joinProductNames($copied));
            $this->_getSession()->addSuccess(
                __('%1 items were copied to %2: %3.', count($copied), $wishlistName, $names)
            );
        }
        $this->_redirectReferer();
    }

    /**
     * Move item to given wishlist.
     * Check whether item belongs to one of customer's wishlists
     *
     * @param \Magento\Wishlist\Model\Item $item
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $customerWishlists
     * @param int $qty
     * @throws \InvalidArgumentException|\DomainException
     */
    protected function _moveItem(
        \Magento\Wishlist\Model\Item $item,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Wishlist\Model\Resource\Wishlist\Collection $customerWishlists,
        $qty = null
    ) {
        if (!$item->getId()) {
            throw new \InvalidArgumentException();
        }
        if ($item->getWishlistId() == $wishlist->getId()) {
            throw new \DomainException(null, 1);
        }
        if (!$customerWishlists->getItemById($item->getWishlistId())) {
            throw new \DomainException(null, 2);
        }

        $buyRequest = $item->getBuyRequest();
        if ($qty) {
            $buyRequest->setQty($qty);
        }
        $wishlist->addNewItem($item->getProduct(), $buyRequest);
        $qtyDiff = $item->getQty() - $qty;
        if ($qty && $qtyDiff > 0) {
            $item->setQty($qtyDiff);
            $item->save();
        } else {
            $item->delete();
        }
    }

    /**
     * Move wishlist item to given wishlist
     *
     * @return void
     */
    public function moveitemAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = $this->getRequest()->getParam('item_id');

        if ($itemId) {
            try {
                /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists */
                $wishlists = $this->_wishlistCollectionFactory->create();
                $wishlists->filterByCustomerId($this->_getSession()->getCustomerId());

                /* @var \Magento\Wishlist\Model\Item $item */
                $item = $this->_itemFactory->create();
                $item->loadWithOptions($itemId);

                $productName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($item->getProduct()->getName());
                $wishlistName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName());

                $this->_moveItem($item, $wishlist, $wishlists, $this->getRequest()->getParam('qty', null));
                $this->_getSession()->addSuccess(
                    __('"%1" was moved to %2.', $productName, $wishlistName)
                );
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            } catch (\InvalidArgumentException $e) {
                $this->_getSession()->addError(
                    __("An item with this ID doesn't exist.")
                );
            } catch (\DomainException $e) {
                if ($e->getCode() == 1) {
                    $this->_getSession()->addError(
                        __('"%1" is already present in %2.', $productName, $wishlistName)
                    );
                } else {
                    $this->_getSession()->addError(
                        __('We cannot move "%1".', $productName)
                    );
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_getSession()->addException($e,
                    __('We could not move the wish list item.')
                );
            }
        }
        $wishlist->save();
        $this->_redirectReferer();
    }

    /**
     * Move wishlist items to given wishlist
     */
    public function moveitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemIds = $this->getRequest()->getParam('selected', array());
        $moved = array();
        $failed = array();
        $notFound = array();
        $notAllowed = array();
        $alreadyPresent = array();
        if (count($itemIds)) {
            /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists */
            $wishlists = $this->_wishlistCollectionFactory->create();
            $wishlists->filterByCustomerId($this->_getSession()->getCustomerId());
            $qtys = $this->getRequest()->getParam('qty', array());

            foreach ($itemIds as $id => $value) {
                try {
                    /* @var \Magento\Wishlist\Model\Item $item */
                    $item = $this->_itemFactory->create();
                    $item->loadWithOptions($id);

                    $this->_moveItem($item, $wishlist, $wishlists, isset($qtys[$id]) ? $qtys[$id] : null);
                    $moved[$id] = $item;
                } catch (\InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (\DomainException $e) {
                    if ($e->getCode() == 1) {
                        $alreadyPresent[$id] = $item;
                    } else {
                        $notAllowed[$id] = $item;
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }

        $wishlistName = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName());

        if (count($notFound)) {
            $this->_getSession()->addError(
                __('%1 items were not found.', count($notFound))
            );
        }

        if (count($notAllowed)) {
            $names = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($this->_joinProductNames($notAllowed));
            $this->_getSession()->addError(
                __('%1 items cannot be moved: %2.', count($notAllowed), $names)
            );
        }

        if (count($alreadyPresent)) {
            $names = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->_getSession()->addError(
                __('%1 items are already present in %2: %3.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($failed)) {
            $this->_getSession()->addError(
                __('We could not move %1 items.', count($failed))
            );
        }

        if (count($moved)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Core\Helper\Data')->escapeHtml($this->_joinProductNames($moved));
            $this->_getSession()->addSuccess(
                __('%1 items were moved to %2: %3.', count($moved), $wishlistName, $names)
            );
        }
        $this->_redirectReferer();
    }
}
