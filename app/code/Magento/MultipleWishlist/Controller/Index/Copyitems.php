<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Index;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\MultipleWishlist\Controller\IndexInterface;
use Magento\MultipleWishlist\Model\ItemManager;
use Magento\Wishlist\Controller\WishlistProviderInterface;

class Copyitems extends \Magento\Framework\App\Action\Action implements IndexInterface
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\MultipleWishlist\Model\ItemManager
     */
    protected $itemManager;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param ItemManager $itemManager
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     */
    public function __construct(
        Context $context,
        WishlistProviderInterface $wishlistProvider,
        ItemManager $itemManager,
        \Magento\Wishlist\Model\ItemFactory $itemFactory
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->itemManager = $itemManager;
        $this->itemFactory = $itemFactory;
        parent::__construct($context);
    }

    /**
     * Copy wishlist items to given wishlist
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
        $notFound = [];
        $alreadyPresent = [];
        $failed = [];
        $copied = [];
        if (count($itemIds)) {
            $qtys = $this->getRequest()->getParam('qty', []);
            foreach ($itemIds as $id => $value) {
                try {
                    /* @var \Magento\Wishlist\Model\Item $item */
                    $item = $this->itemFactory->create();
                    $item->loadWithOptions($id);

                    $this->itemManager->copy($item, $wishlist, isset($qtys[$id]) ? $qtys[$id] : null);
                    $copied[$id] = $item;
                } catch (\InvalidArgumentException $e) {
                    $notFound[] = $id;
                } catch (\DomainException $e) {
                    $alreadyPresent[$id] = $item;
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                    $failed[] = $id;
                }
            }
        }
        $wishlistName = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($wishlist->getName());

        $wishlist->save();

        if (count($notFound)) {
            $this->messageManager->addError(__('%1 items were not found.', count($notFound)));
        }

        if (count($failed)) {
            $this->messageManager->addError(__('We could not copy %1 items.', count($failed)));
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

        if (count($copied)) {
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
            $names = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($this->joinProductNames($copied));
            $this->messageManager->addSuccess(
                __('%1 items were copied to %2: %3.', count($copied), $wishlistName, $names)
            );
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
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
}
