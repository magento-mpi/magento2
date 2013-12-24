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
use Magento\App\Action\NotFoundException;
use Magento\App\RequestInterface;

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
     * List of protected actions
     *
     * @var array
     */
    protected $_protectedActions = array(
        'createwishlist', 'editwishlist', 'deletewishlist', 'copyitems', 'moveitem', 'moveitems'
    );

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     * @param \Magento\App\Response\Http\FileFactory $fileResponseFactory
     * @param \Magento\Core\App\Action\FormKeyValidator $formKeyValidator
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Session\Generic $wishlistSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Model\Config $wishlistConfig,
        \Magento\App\Response\Http\FileFactory $fileResponseFactory,
        \Magento\Core\App\Action\FormKeyValidator $formKeyValidator,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Session\Generic $wishlistSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistCollectionFactory
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_wishlistSession = $wishlistSession;
        $this->_customerSession = $customerSession;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        parent::__construct($context, $coreRegistry, $wishlistConfig, $fileResponseFactory, $formKeyValidator);
    }

    /**
     * Check if multiple wishlist is enabled on current store before all other actions
     *
     * @param RequestInterface $request
     * @return \Magento\App\ResponseInterface
     * @throws \Magento\App\Action\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled()
            && in_array($request->getActionName(), $this->_protectedActions)
        ) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
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
                $this->messageManager->addSuccess(
                    __('Wish List "%1" was saved.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName()))
                );
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
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
                $this->getResponse()->setRedirect($helper->getListUrl());
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
     * @return \Magento\App\Action\Action|\Zend_Controller_Response_Abstract
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

            $this->messageManager->addSuccess(
                __('Wish List "%1" was saved.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName()))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong creating the wish list.')
            );
        }

        if (!$wishlist || !$wishlist->getId()) {
            $this->messageManager->addError('Could not create wishlist');
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
     * @throws NotFoundException
     */
    public function deletewishlistAction()
    {
        try {
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                throw new NotFoundException();
            }
            if ($this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isWishlistDefault($wishlist)) {
                throw new \Magento\Core\Exception(__('The default wish list cannot be deleted.'));
            }
            $wishlist->delete();
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $this->messageManager->addSuccess(
                __('Wish list "%1" has been deleted.', $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName()))
            );
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong deleting the wish list.');
            $this->messageManager->addException($e, $message);
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
     * @throws NotFoundException
     */
    public function copyitemAction()
    {
        $requestParams = $this->getRequest()->getParams();
        if ($this->_getSession()->getBeforeWishlistRequest()) {
            $requestParams = $this->_getSession()->getBeforeWishlistRequest();
            $this->_getSession()->unsBeforeWishlistRequest();
        }

        $wishlist = $this->_getWishlist(isset($requestParams['wishlist_id']) ? $requestParams['wishlist_id'] : null);
        if (!$wishlist) {
            throw new NotFoundException();
        }
        $itemId = isset($requestParams['item_id']) ? $requestParams['item_id'] : null;
        $qty = isset($requestParams['qty']) ? $requestParams['qty'] : null;
        if ($itemId) {
            $productName = '';
            try {
                /* @var \Magento\Wishlist\Model\Item $item */
                $item = $this->_itemFactory->create();
                $item->loadWithOptions($itemId);

                $wishlistName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName());
                $productName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($item->getProduct()->getName());

                $this->_copyItem($item, $wishlist, $qty);
                $this->messageManager->addSuccess(
                    __('"%1" was copied to %2.', $productName, $wishlistName)
                );
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            } catch (\InvalidArgumentException $e) {
                $this->messageManager->addError(
                    __('The item was not found.')
                );
            } catch (\DomainException $e) {
                $this->messageManager->addError(
                    __('"%1" is already present in %2.', $productName, $wishlistName)
                );
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                if ($productName) {
                    $message = __('We could not copy "%1".', $productName);
                } else {
                    $message = __('We could not copy the wish list item.');
                }
                $this->messageManager->addError($message);
            }
        }
        $wishlist->save();
        if ($this->_getSession()->hasBeforeWishlistUrl()) {
            $this->getResponse()->setRedirect($this->_getSession()->getBeforeWishlistUrl());
            $this->_getSession()->unsBeforeWishlistUrl();
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }

    /**
     * Copy wishlist items to given wishlist
     *
     * @throws NotFoundException
     */
    public function copyitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
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
                    $this->_objectManager->get('Magento\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }
        $wishlistName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName());

        $wishlist->save();

        if (count($notFound)) {
            $this->messageManager->addError(
                __('%1 items were not found.', count($notFound))
            );
        }

        if (count($failed)) {
            $this->messageManager->addError(
                __('We could not copy %1 items.', count($failed))
            );
        }

        if (count($alreadyPresent)) {
            $names = $this->_objectManager->get('Magento\Escaper')
                ->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->messageManager->addError(
                __('%1 items are already present in %2: %3.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($copied)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Escaper')->escapeHtml($this->_joinProductNames($copied));
            $this->messageManager->addSuccess(
                __('%1 items were copied to %2: %3.', count($copied), $wishlistName, $names)
            );
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
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
     * @throws NotFoundException
     */
    public function moveitemAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
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

                $productName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($item->getProduct()->getName());
                $wishlistName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName());

                $this->_moveItem($item, $wishlist, $wishlists, $this->getRequest()->getParam('qty', null));
                $this->messageManager->addSuccess(
                    __('"%1" was moved to %2.', $productName, $wishlistName)
                );
                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            } catch (\InvalidArgumentException $e) {
                $this->messageManager->addError(
                    __("An item with this ID doesn't exist.")
                );
            } catch (\DomainException $e) {
                if ($e->getCode() == 1) {
                    $this->messageManager->addError(
                        __('"%1" is already present in %2.', $productName, $wishlistName)
                    );
                } else {
                    $this->messageManager->addError(
                        __('We cannot move "%1".', $productName)
                    );
                }
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e,
                    __('We could not move the wish list item.')
                );
            }
        }
        $wishlist->save();
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }

    /**
     * Move wishlist items to given wishlist
     * @throws NotFoundException
     */
    public function moveitemsAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
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
                    $this->_objectManager->get('Magento\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }

        $wishlistName = $this->_objectManager->get('Magento\Escaper')->escapeHtml($wishlist->getName());

        if (count($notFound)) {
            $this->messageManager->addError(
                __('%1 items were not found.', count($notFound))
            );
        }

        if (count($notAllowed)) {
            $names = $this->_objectManager->get('Magento\Escaper')->escapeHtml($this->_joinProductNames($notAllowed));
            $this->messageManager->addError(
                __('%1 items cannot be moved: %2.', count($notAllowed), $names)
            );
        }

        if (count($alreadyPresent)) {
            $names = $this->_objectManager->get('Magento\Escaper')->escapeHtml($this->_joinProductNames($alreadyPresent));
            $this->messageManager->addError(
                __('%1 items are already present in %2: %3.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($failed)) {
            $this->messageManager->addError(
                __('We could not move %1 items.', count($failed))
            );
        }

        if (count($moved)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Escaper')->escapeHtml($this->_joinProductNames($moved));
            $this->messageManager->addSuccess(
                __('%1 items were moved to %2: %3.', count($moved), $wishlistName, $names)
            );
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
