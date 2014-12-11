<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Controller\Search;

use Magento\Framework\App\Action\Action;

class Addtocart extends \Magento\MultipleWishlist\Controller\Search
{
    /**
     * @var \Magento\Wishlist\Model\LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\MultipleWishlist\Model\SearchFactory $searchFactory
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory $strategyEmailFactory
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory $strategyNameFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\MultipleWishlist\Model\SearchFactory $searchFactory,
        \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory $strategyEmailFactory,
        \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory $strategyNameFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor
    ) {
        $this->quantityProcessor = $quantityProcessor;

        parent::__construct(
            $context,
            $coreRegistry,
            $itemFactory,
            $wishlistFactory,
            $searchFactory,
            $strategyEmailFactory,
            $strategyNameFactory,
            $checkoutSession,
            $checkoutCart,
            $customerSession,
            $localeResolver,
            $moduleManager
        );
    }

    /**
     * Add wishlist item to cart
     *
     * @return void
     */
    public function execute()
    {
        $messages = [];
        $addedItems = [];
        $notSalable = [];
        $hasOptions = [];

        /** @var \Magento\Checkout\Model\Cart $cart  */
        $cart = $this->_checkoutCart;
        $qtys = $this->getRequest()->getParam('qty');
        $selected = $this->getRequest()->getParam('selected');
        foreach ($qtys as $itemId => $qty) {
            if ($qty && isset($selected[$itemId])) {
                try {
                    /** @var \Magento\Wishlist\Model\Item $item*/
                    $item = $this->_itemFactory->create();
                    $item->loadWithOptions($itemId);
                    $item->unsProduct();
                    $qty = $this->qtyProcessor->process($qty);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                    if ($item->addToCart($cart, false)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (\Magento\Framework\Model\Exception $e) {
                    if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } elseif ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = __('%1 for "%2"', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
                    $messages[] = __('We could not add the item to shopping cart.');
                }
            }
        }

        if ($this->_objectManager->get('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
            $redirectUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
        } elseif ($this->_redirect->getRefererUrl()) {
            $redirectUrl = $this->_redirect->getRefererUrl();
        }

        if ($notSalable) {
            $products = [];
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Cannot add the following product(s) to shopping cart: %1.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = [];
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __(
                'Product(s) %1 have required options. Each product can only be added individually.',
                join(', ', $products)
            );
        }

        if ($messages) {
            if (count($messages) == 1 && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                $redirectUrl = $item->getProductUrl();
            } else {
                foreach ($messages as $message) {
                    $this->messageManager->addError($message);
                }
            }
        }

        if ($addedItems) {
            $products = [];
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            $this->messageManager->addSuccess(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedItems), join(', ', $products))
            );
        }

        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        $this->getResponse()->setRedirect($redirectUrl);
    }
}
