<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multiple wishlist frontend search controller
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Controller_Search extends Mage_Core_Controller_Front_Action
{
    /**
     * Localization filter
     *
     * @var Zend_Filter_LocalizedToNormalized
     */
    protected $_localFilter;

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
     * @return Enterprise_Wishlist_Controller_Search
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::helper('Enterprise_Wishlist_Helper_Data')->isModuleEnabled()) {
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Get current customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wish List Search'));
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
                    Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Please specify correct search options.')
                );
            };

            $strategy = null;
            switch ($params['search']) {
                case 'type':
                    $strategy = Mage::getModel('Enterprise_Wishlist_Model_Search_Strategy_Name');
                    break;
                case 'email':
                    $strategy = Mage::getModel('Enterprise_Wishlist_Model_Search_Strategy_Email');
                    break;
                default:
                    Mage::throwException(
                        Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Please specify correct search options.')
                    );
            }

            $strategy->setSearchParams($params);
            $search = Mage::getModel('Enterprise_Wishlist_Model_Search');
            Mage::register('search_results', $search->getResults($strategy));
            $this->_getSession()->setLastWishlistSearchParams($params);
        } catch (InvalidArgumentException $e) {
            $this->_getSession()->addNotice($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not perform the search.'));
        }

        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wish List Search'));
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
        $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist');
        $wishlist->load($wishlistId);
        if (!$wishlist->getId()
            || (!$wishlist->getVisibility() && $wishlist->getCustomerId != $this->_getSession()->getCustomerId())) {
            return $this->norouteAction();
        }
        Mage::register('wishlist', $wishlist);
        $this->loadLayout();
        $block = $this->getLayout()->getBlock('customer.wishlist.info');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->_initLayoutMessages(array('Mage_Customer_Model_Session', 'Mage_Checkout_Model_Session', 'Mage_Wishlist_Model_Session'));
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

        /** @var Mage_Checkout_Model_Cart $cart  */
        $cart = Mage::getSingleton('Mage_Checkout_Model_Cart');
        $qtys = $this->getRequest()->getParam('qty');
        $selected = $this->getRequest()->getParam('selected');
        foreach ($qtys as $itemId => $qty) {
            if ($qty && isset($selected[$itemId])) {
                try {
                    /** @var Mage_Wishlist_Model_Item $item*/
                    $item = Mage::getModel('Mage_Wishlist_Model_Item');
                    $item->loadWithOptions($itemId);
                    $item->unsProduct();
                    $qty = $this->_processLocalizedQty($qty);
                    if ($qty) {
                        $item->setQty($qty);
                    }
                    if ($item->addToCart($cart, false)) {
                        $addedItems[] = $item->getProduct();
                    }
                } catch (Mage_Core_Exception $e) {
                    if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                        $notSalable[] = $item;
                    } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                        $hasOptions[] = $item;
                    } else {
                        $messages[] = $this->__('%s for "%s"', trim($e->getMessage(), '.'), $item->getProduct()->getName());
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    $messages[] = Mage::helper('Enterprise_Wishlist_Helper_Data')->__('We could not add the item to shopping cart.');
                }
            }
        }

        if (Mage::helper('Mage_Checkout_Helper_Cart')->getShouldRedirectToCart()) {
            $redirectUrl = Mage::helper('Mage_Checkout_Helper_Cart')->getCartUrl();
        } else if ($this->_getRefererUrl()) {
            $redirectUrl = $this->_getRefererUrl();
        }

        if ($notSalable) {
            $products = array();
            foreach ($notSalable as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('Mage_Wishlist_Helper_Data')->__('Cannot add the following product(s) to shopping cart: %s.', join(', ', $products));
        }

        if ($hasOptions) {
            $products = array();
            foreach ($hasOptions as $item) {
                $products[] = '"' . $item->getProduct()->getName() . '"';
            }
            $messages[] = Mage::helper('Mage_Wishlist_Helper_Data')->__('Product(s) %s have required options. Each product can only be added individually.', join(', ', $products));
        }

        if ($messages) {
            if ((count($messages) == 1) && count($hasOptions) == 1) {
                $item = $hasOptions[0];
                $redirectUrl = $item->getProductUrl();
            } else {
                $wishlistSession = Mage::getSingleton('Mage_Checkout_Model_Session');
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

            Mage::getSingleton('Mage_Checkout_Model_Session')->addSuccess(
                Mage::helper('Mage_Wishlist_Helper_Data')->__('%d product(s) have been added to shopping cart: %s.', count($addedItems), join(', ', $products))
            );
        }

        // save cart and collect totals
        $cart->save()->getQuote()->collectTotals();

        $this->_redirectUrl($redirectUrl);
    }
}
