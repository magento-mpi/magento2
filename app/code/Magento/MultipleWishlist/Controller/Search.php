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
class Search extends \Magento\App\Action\Action
{
    /**
     * Localization filter
     *
     * @var \Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Checkout cart
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_checkoutCart;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Strategy name factory
     *
     * @var \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory
     */
    protected $_strategyNameFactory;

    /**
     * Strategy email factory
     *
     * @var \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory
     */
    protected $_strategyEmailFactory;

    /**
     * Search factory
     *
     * @var \Magento\MultipleWishlist\Model\SearchFactory
     */
    protected $_searchFactory;

    /**
     * Wishlist factory
     *
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * Item model factory
     *
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Construct
     *
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\MultipleWishlist\Model\SearchFactory $searchFactory
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory $strategyEmailFactory
     * @param \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory $strategyNameFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\Cart $checkoutCart
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\MultipleWishlist\Model\SearchFactory $searchFactory,
        \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory $strategyEmailFactory,
        \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory $strategyNameFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Locale\ResolverInterface $localeResolver
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_searchFactory = $searchFactory;
        $this->_strategyEmailFactory = $strategyEmailFactory;
        $this->_strategyNameFactory = $strategyNameFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutCart = $checkoutCart;
        $this->_customerSession = $customerSession;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context);
    }

    /**
     * Processes localized qty (entered by user at frontend) into internal php format
     *
     * @param string $qty
     * @return float|int|null
     */
    protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter) {
            $this->_localFilter = new \Zend_Filter_LocalizedToNormalized(
                array('locale' => $this->_locale->getLocaleCode())
            );
        }
        $qty = $this->_localFilter->filter($qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;
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
        if (!$this->_objectManager->get('Magento\MultipleWishlist\Helper\Data')->isModuleEnabled()) {
            throw new NotFoundException();
        }
        return parent::dispatch($request);
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->_view->renderLayout();
    }

    /**
     * Wishlist search action
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function resultsAction()
    {
        $this->_view->loadLayout();

        try {
            $params = $this->getRequest()->getParam('params');
            if (empty($params) || !is_array($params) || empty($params['search'])) {
                throw new \Magento\Core\Exception(__('Please specify correct search options.'));
            };

            $strategy = null;
            switch ($params['search']) {
                case 'type':
                    $strategy = $this->_strategyNameFactory->create();
                    break;
                case 'email':
                    $strategy = $this->_strategyEmailFactory->create();
                    break;
                default:
                    throw new \Magento\Core\Exception(__('Please specify correct search options.'));
            }

            $strategy->setSearchParams($params);
            /** @var \Magento\MultipleWishlist\Model\Search $search */
            $search = $this->_searchFactory->create();
            $this->_coreRegistry->register('search_results', $search->getResults($strategy));
            $this->_customerSession->setLastWishlistSearchParams($params);
        } catch (\InvalidArgumentException $e) {
            $this->messageManager->addNotice($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not perform the search.'));
        }

        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $headBlock = $layout->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->_view->renderLayout();
    }

    /**
     * View customer wishlist
     *
     * @return void
     * @throws NotFoundException
     */
    public function viewAction()
    {
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$wishlistId) {
            throw new NotFoundException();
        }
        /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
        $wishlist = $this->_wishlistFactory->create();
        $wishlist->load($wishlistId);
        if (!$wishlist->getId()
            || (!$wishlist->getVisibility() && $wishlist->getCustomerId != $this->_customerSession->getCustomerId())) {
            throw new NotFoundException();
        }
        $this->_coreRegistry->register('wishlist', $wishlist);
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('customer.wishlist.info');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * Add wishlist item to cart
     *
     * @return void
     */
    public function addtocartAction()
    {
        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

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
                    $qty = $this->_processLocalizedQty($qty);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                    if ($item->addToCart($cart, false)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (\Magento\Core\Exception $e) {
                    if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } elseif ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = __('%1 for "%2"', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get('Magento\Logger')->logException($e);
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
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Cannot add the following product(s) to shopping cart: %1.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = __('Product(s) %1 have required options. Each product can only be added individually.', join(', ', $products));
        }

        if ($messages) {
            if ((count($messages) == 1) && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                $redirectUrl = $item->getProductUrl();
            } else {
                foreach ($messages as $message) {
                    $this->messageManager->addError($message);
                }
            }
        }

        if ($addedItems) {
            $products = array();
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
