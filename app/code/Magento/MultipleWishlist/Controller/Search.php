<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist frontend search controller
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_MultipleWishlist_Controller_Search extends Magento_Core_Controller_Front_Action
{
    /**
     * Localization filter
     *
     * @var Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
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
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
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
     * @return Magento_MultipleWishlist_Controller_Search
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_objectManager->get('Magento_MultipleWishlist_Helper_Data')->isModuleEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Get current customer session
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->renderLayout();
    }

    /**
     * Wishlist search action
     */
    public function resultsAction()
    {
        $this->loadLayout();

        try {
            $params = $this->getRequest()->getParam('params');
            if (empty($params) || !is_array($params) || empty($params['search'])) {
                Mage::throwException(
                    __('Please specify correct search options.')
                );
            };

            $strategy = null;
            switch ($params['search']) {
                case 'type':
                    $strategy = Mage::getModel('Magento_MultipleWishlist_Model_Search_Strategy_Name');
                    break;
                case 'email':
                    $strategy = Mage::getModel('Magento_MultipleWishlist_Model_Search_Strategy_Email');
                    break;
                default:
                    Mage::throwException(
                        __('Please specify correct search options.')
                    );
            }

            $strategy->setSearchParams($params);
            $search = Mage::getModel('Magento_MultipleWishlist_Model_Search');
            $this->_coreRegistry->register('search_results', $search->getResults($strategy));
            $this->_getSession()->setLastWishlistSearchParams($params);
        } catch (InvalidArgumentException $e) {
            $this->_getSession()->addNotice($e->getMessage());
        } catch (Magento_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(__('We could not perform the search.'));
        }

        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Wish List Search'));
        }
        $this->renderLayout();
    }

    /**
     * View customer wishlist
     */
    public function viewAction()
    {
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        if (!$wishlistId) {
            return $this->norouteAction();
        }
        $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist');
        $wishlist->load($wishlistId);
        if (!$wishlist->getId()
            || (!$wishlist->getVisibility() && $wishlist->getCustomerId != $this->_getSession()->getCustomerId())) {
            return $this->norouteAction();
        }
        $this->_coreRegistry->register('wishlist', $wishlist);
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('customer.wishlist.info');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->_initLayoutMessages(array('Magento_Customer_Model_Session', 'Magento_Checkout_Model_Session', 'Magento_Wishlist_Model_Session'));
        $this->renderLayout();
    }

    /**
     * Add wishlist item to cart
     */
    public function addtocartAction()
    {
        $messages   = array();
        $addedItems = array();
        $notSalable = array();
        $hasOptions = array();

        /** @var Magento_Checkout_Model_Cart $cart  */
        $cart = Mage::getSingleton('Magento_Checkout_Model_Cart');
        $qtys = $this->getRequest()->getParam('qty');
        $selected = $this->getRequest()->getParam('selected');
        foreach ($qtys as $itemId => $qty) {
            if ($qty && isset($selected[$itemId])) {
                try {
                    /** @var Magento_Wishlist_Model_Item $item*/
                    $item = Mage::getModel('Magento_Wishlist_Model_Item');
                    $item->loadWithOptions($itemId);
                    $item->unsProduct();
                    $qty = $this->_processLocalizedQty($qty);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                    if ($item->addToCart($cart, false)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (Magento_Core_Exception $e) {
                    if ($e->getCode() == Magento_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } else if ($e->getCode() == Magento_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = __('%1 for "%2"', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $messages[] = __('We could not add the item to shopping cart.');
                }
            }
        }

        if ($this->_objectManager->get('Magento_Checkout_Helper_Cart')->getShouldRedirectToCart()) {
            $redirectUrl = $this->_objectManager->get('Magento_Checkout_Helper_Cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
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
                $wishlistSession = Mage::getSingleton('Magento_Checkout_Model_Session');
                foreach ($messages as $message) {
                    $wishlistSession->addError($message);
                }
            }
        }

        if ($addedItems) {
            $products = array();
            foreach ($addedItems as $product) {
                $products[] = '"' . $product->getName() . '"';
            }

            Mage::getSingleton('Magento_Checkout_Model_Session')->addSuccess(
                __('%1 product(s) have been added to shopping cart: %2.', count($addedItems), join(', ', $products))
            );
        }

        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        $this->_redirectUrl($redirectUrl);
    }
}
