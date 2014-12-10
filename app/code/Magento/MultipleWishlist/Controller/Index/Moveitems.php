<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\NotFoundException;
use Magento\MultipleWishlist\Controller\IndexInterface;
use Magento\MultipleWishlist\Model\ItemManager;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Moveitems extends \Magento\Framework\App\Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
     */
    protected $wishlistColFactory;

    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\MultipleWishlist\Model\ItemManager
     */
    protected $itemManager;

    /**
     * @param Context $context
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory
     * @param WishlistProviderInterface $wishlistProvider
     * @param Session $customerSession
     * @param ItemManager $itemManager
     */
    public function __construct(
        Context $context,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory,
        WishlistProviderInterface $wishlistProvider,
        Session $customerSession,
        ItemManager $itemManager
    ) {
        $this->itemFactory = $itemFactory;
        $this->wishlistColFactory = $wishlistColFactory;
        $this->wishlistProvider = $wishlistProvider;
        $this->customerSession = $customerSession;
        $this->itemManager = $itemManager;
        parent::__construct($context);
    }

    /**
     * Join item product names
     *
     * @param array $items
     * @return string
     */
    protected function joinProductNames($items)
    {
        return join(
            ', ',
            array_map(
                function ($item) {
                    return '"' . $item->getProduct()->getName() . '"';
                },
                $items
            )
        );
    }

    /**
     * Move wishlist items to given wishlist
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            throw new NotFoundException();
        }
        $itemIds = $this->getRequest()->getParam('selected', []);
        $moved = [];
        $failed = [];
        $notFound = [];
        $notAllowed = [];
        $alreadyPresent = [];
        if (count($itemIds)) {
            /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists */
            $wishlists = $this->wishlistColFactory->create();
            $wishlists->filterByCustomerId($this->customerSession->getCustomerId());
            $qtys = $this->getRequest()->getParam('qty', []);

            foreach ($itemIds as $id => $value) {
                try {
                    /* @var \Magento\Wishlist\Model\Item $item */
                    $item = $this->itemFactory->create();
                    $item->loadWithOptions($id);

                    $this->itemManager->move($item, $wishlist, $wishlists, isset($qtys[$id]) ? $qtys[$id] : null);
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
                    $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }

        $wishlistName = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName());

        if (count($notFound)) {
            $this->messageManager->addError(__('%1 items were not found.', count($notFound)));
        }

        if (count($notAllowed)) {
            $names = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($this->joinProductNames($notAllowed));
            $this->messageManager->addError(__('%1 items cannot be moved: %2.', count($notAllowed), $names));
        }

        if (count($alreadyPresent)) {
            $names = $this->_objectManager->get(
                'Magento\Framework\Escaper'
            )->escapeHtml(
                $this->joinProductNames($alreadyPresent)
            );
            $this->messageManager->addError(
                __('%1 items are already present in %2: %3.', count($alreadyPresent), $wishlistName, $names)
            );
        }

        if (count($failed)) {
            $this->messageManager->addError(__('We could not move %1 items.', count($failed)));
        }

        if (count($moved)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($this->joinProductNames($moved));
            $this->messageManager->addSuccess(
                __('%1 items were moved to %2: %3.', count($moved), $wishlistName, $names)
            );
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
