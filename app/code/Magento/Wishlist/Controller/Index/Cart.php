<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Wishlist\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Wishlist\Controller\IndexInterface;

class Cart extends Action\Action implements IndexInterface
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Wishlist\Model\LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor
     */
    public function __construct(
        Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        parent::__construct($context);
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $itemId = (int)$this->getRequest()->getParam('item');

        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->_redirect('*/*');
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->quantityProcessor->process($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session \Magento\Framework\Session\Generic */
        $session = $this->_objectManager->get('Magento\Wishlist\Model\Session');
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');

        $redirectUrl = $this->_url->getUrl('*/*');

        try {
            $options = $this->_objectManager->create(
                'Magento\Wishlist\Model\Item\Option'
            )->getCollection()->addItemFilter(
                [$itemId]
            );
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = $this->_objectManager->get(
                'Magento\Catalog\Helper\Product'
            )->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                ['current_config' => $item->getBuyRequest()]
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            if (!$cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($item->getProduct()->getName())
                );
                $this->messageManager->addSuccess($message);
            }

            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

            if ($this->_objectManager->get('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
                $redirectUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
            } else {
                $refererUrl = $this->_redirect->getRefererUrl();
                if ($refererUrl &&
                    ($refererUrl != $this->_objectManager->get('Magento\Framework\UrlInterface')
                            ->getUrl('*/*/configure/', ['id' => $item->getId()])
                    )
                ) {
                    $redirectUrl = $refererUrl;
                }
            }
            $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
        } catch (\Magento\Framework\Model\Exception $e) {
            if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                $this->messageManager->addError(__('This product(s) is out of stock.'));
            } elseif ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                $this->messageManager->addNotice($e->getMessage());
                $redirectUrl = $this->_url->getUrl('*/*/configure/', ['id' => $item->getId()]);
            } else {
                $this->messageManager->addNotice($e->getMessage());
                $redirectUrl = $this->_url->getUrl('*/*/configure/', ['id' => $item->getId()]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Cannot add item to shopping cart'));
        }

        $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();

        return $this->getResponse()->setRedirect($redirectUrl);
    }
}
