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
 * Wishlist front controller
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Controller;

class Index
    extends \Magento\Wishlist\Controller\AbstractController
    implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * @var \Magento\Wishlist\Model\Config
     */
    protected $_wishlistConfig;

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * If true, authentication in this controller (wishlist) could be skipped
     *
     * @var bool
     */
    protected $_skipAuthentication = false;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Wishlist\Model\Config $wishlistConfig
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Wishlist\Model\Config $wishlistConfig
    ) {
        parent::__construct($context);
        $this->_wishlistConfig = $wishlistConfig;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_skipAuthentication && !\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');
            if (!$customerSession->getBeforeWishlistUrl()) {
                $customerSession->setBeforeWishlistUrl($this->_getRefererUrl());
            }
            $customerSession->setBeforeWishlistRequest($this->getRequest()->getParams());
        }
        if (!\Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Set skipping authentication in actions of this controller (wishlist)
     *
     * @return \Magento\Wishlist\Controller\Index
     */
    public function skipAuthentication()
    {
        $this->_skipAuthentication = true;
        return $this;
    }

    /**
     * Retrieve wishlist object
     * @param int $wishlistId
     * @return \Magento\Wishlist\Model\Wishlist|bool
     */
    protected function _getWishlist($wishlistId = null)
    {
        $wishlist = \Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
            /* @var \Magento\Wishlist\Model\Wishlist $wishlist */
            $wishlist = \Mage::getModel('\Magento\Wishlist\Model\Wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                \Mage::throwException(
                    __("The requested wish list doesn\'t exist.")
                );
            }

            \Mage::register('wishlist', $wishlist);
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->addException($e,
                __('Wish List could not be created.')
            );
            return false;
        }

        return $wishlist;
    }

    /**
     * Display customer wishlist
     */
    public function indexAction()
    {
        if (!$this->_getWishlist()) {
            return $this->norouteAction();
        }
        $this->loadLayout();

        $session = \Mage::getSingleton('Magento\Customer\Model\Session');
        $block   = $this->getLayout()->getBlock('customer.wishlist');
        $referer = $session->getAddActionReferer(true);
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
            if ($referer) {
                $block->setRefererUrl($referer);
            }
        }

        $this->_initLayoutMessages('\Magento\Customer\Model\Session');
        $this->_initLayoutMessages('\Magento\Checkout\Model\Session');
        $this->_initLayoutMessages('\Magento\Catalog\Model\Session');
        $this->_initLayoutMessages('Magento_Wishlist_Model_Session');

        $this->renderLayout();
    }

    /**
     * Adding new item
     */
    public function addAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $session = \Mage::getSingleton('Magento\Customer\Model\Session');

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = \Mage::getModel('\Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError(__('We can\'t specify a product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $requestParams = $this->getRequest()->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }
            $buyRequest = new \Magento\Object($requestParams);

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                \Mage::throwException($result);
            }
            $wishlist->save();

            $this->_eventManager->dispatch(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            /** @var $helper \Magento\Wishlist\Helper\Data */
            $helper = \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
            $message = __('%1 has been added to your wishlist. Click <a href="%2">here</a> to continue shopping.', $helper->escapeHtml($product->getName()), \Mage::helper('Magento\Core\Helper\Data')->escapeUrl($referer));
            $session->addSuccess($message);
        }
        catch (\Magento\Core\Exception $e) {
            $session->addError(__('An error occurred while adding item to wish list: %1', $e->getMessage()));
        }
        catch (\Exception $e) {
            $session->addError(__('An error occurred while adding item to wish list.'));
            \Mage::logException($e);
        }

        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Action to reconfigure wishlist item
     */
    public function configureAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        try {
            /* @var $item \Magento\Wishlist\Model\Item */
            $item = \Mage::getModel('\Magento\Wishlist\Model\Item');
            $item->loadWithOptions($id);
            if (!$item->getId()) {
                \Mage::throwException(__('We can\'t load the wish list item.'));
            }
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                return $this->norouteAction();
            }

            \Mage::register('wishlist_item', $item);

            $params = new \Magento\Object();
            $params->setCategoryId(false);
            $params->setConfigureMode(true);
            $buyRequest = $item->getBuyRequest();
            if (!$buyRequest->getQty() && $item->getQty()) {
                $buyRequest->setQty($item->getQty());
            }
            if ($buyRequest->getQty() && !$item->getQty()) {
                $item->setQty($buyRequest->getQty());
                \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
            }
            $params->setBuyRequest($buyRequest);
            \Mage::helper('Magento\Catalog\Helper\Product\View')->prepareAndRender($item->getProductId(), $this, $params);
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError($e->getMessage());
            $this->_redirect('*');
            return;
        } catch (\Exception $e) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('We can\'t configure the product.'));
            \Mage::logException($e);
            $this->_redirect('*');
            return;
        }
    }

    /**
     * Action to accept new configuration for a wishlist item
     */
    public function updateItemOptionsAction()
    {
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');
        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = \Mage::getModel('\Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError(__('We can\'t specify a product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $id = (int) $this->getRequest()->getParam('id');
            /* @var \Magento\Wishlist\Model\Item */
            $item = \Mage::getModel('\Magento\Wishlist\Model\Item');
            $item->load($id);
            $wishlist = $this->_getWishlist($item->getWishlistId());
            if (!$wishlist) {
                $this->_redirect('*/');
                return;
            }

            $buyRequest = new \Magento\Object($this->getRequest()->getParams());

            $wishlist->updateItem($id, $buyRequest)
                ->save();

            \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
            $this->_eventManager->dispatch('wishlist_update_item', array(
                'wishlist' => $wishlist, 'product' => $product, 'item' => $wishlist->getItem($id))
            );

            \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();

            $message = __('%1 has been updated in your wish list.', $product->getName());
            $session->addSuccess($message);
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addError(__('An error occurred while updating wish list.'));
            \Mage::logException($e);
        }
        $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Update wishlist item comments
     */
    public function updateAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $post = $this->getRequest()->getPost();
        if($post && isset($post['description']) && is_array($post['description'])) {
            $updatedItems = 0;

            foreach ($post['description'] as $itemId => $description) {
                $item = \Mage::getModel('\Magento\Wishlist\Model\Item')->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    continue;
                }

                // Extract new values
                $description = (string) $description;

                if ($description == \Mage::helper('Magento\Wishlist\Helper\Data')->defaultCommentString()) {
                    $description = '';
                } elseif (!strlen($description)) {
                    $description = $item->getDescription();
                }

                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->_processLocalizedQty($post['qty'][$itemId]);
                }
                if (is_null($qty)) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (\Exception $e) {
                        \Mage::logException($e);
                        \Mage::getSingleton('Magento\Customer\Model\Session')->addError(
                            __('Can\'t delete item from wishlist')
                        );
                    }
                }

                // Check that we need to save
                if (($item->getDescription() == $description) && ($item->getQty() == $qty)) {
                    continue;
                }
                try {
                    $item->setDescription($description)
                        ->setQty($qty)
                        ->save();
                    $updatedItems++;
                } catch (\Exception $e) {
                    \Mage::getSingleton('Magento\Customer\Model\Session')->addError(
                        __('Can\'t save description %1', \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($description))
                    );
                }
            }

            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $wishlist->save();
                    \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
                }
                catch (\Exception $e) {
                    \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('Can\'t update wish list'));
                }
            }

            if (isset($post['save_and_share'])) {
                $this->_redirect('*/*/share', array('wishlist_id' => $wishlist->getId()));
                return;
            }
        }
        $this->_redirect('*', array('wishlist_id' => $wishlist->getId()));
    }

    /**
     * Remove item
     */
    public function removeAction()
    {
        $id = (int) $this->getRequest()->getParam('item');
        $item = \Mage::getModel('\Magento\Wishlist\Model\Item')->load($id);
        if (!$item->getId()) {
            return $this->norouteAction();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->norouteAction();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(
                __('An error occurred while deleting the item from wish list: %1', $e->getMessage())
            );
        } catch(\Exception $e) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(
                __('An error occurred while deleting the item from wish list.')
            );
        }

        \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();

        $this->_redirectReferer(\Mage::getUrl('*/*'));
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
    public function cartAction()
    {
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item \Magento\Wishlist\Model\Item */
        $item = \Mage::getModel('\Magento\Wishlist\Model\Item')->load($itemId);
        if (!$item->getId()) {
            return $this->_redirect('*/*');
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
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
        $qty = $this->_processLocalizedQty($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        /* @var $session \Magento\Core\Model\Session\Generic */
        $session    = \Mage::getSingleton('Magento_Wishlist_Model_Session');
        $cart       = \Mage::getSingleton('Magento\Checkout\Model\Cart');

        $redirectUrl = \Mage::getUrl('*/*');

        try {
            $options = \Mage::getModel('\Magento\Wishlist\Model\Item\Option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $buyRequest = \Mage::helper('Magento\Catalog\Helper\Product')->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                array('current_config' => $item->getBuyRequest())
            );

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($cart, true);
            $cart->save()->getQuote()->collectTotals();
            $wishlist->save();

            \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();

            if (\Mage::helper('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
                $redirectUrl = \Mage::helper('Magento\Checkout\Helper\Cart')->getCartUrl();
            } else if ($this->_getRefererUrl()) {
                $redirectUrl = $this->_getRefererUrl();
            }
            \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
        } catch (\Magento\Core\Exception $e) {
            if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(__('This product(s) is out of stock.'));
            } else if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                \Mage::getSingleton('Magento\Catalog\Model\Session')->addNotice($e->getMessage());
                $redirectUrl = \Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
            } else {
                \Mage::getSingleton('Magento\Catalog\Model\Session')->addNotice($e->getMessage());
                $redirectUrl = \Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
            }
        } catch (\Exception $e) {
            $session->addException($e, __('Cannot add item to shopping cart'));
        }

        \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();

        return $this->_redirectUrl($redirectUrl);
    }

    /**
     * Add cart item to wishlist and remove from cart
     */
    public function fromcartAction()
    {
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var \Magento\Checkout\Model\Cart $cart */
        $cart = \Mage::getSingleton('Magento\Checkout\Model\Cart');
        $session = \Mage::getSingleton('Magento\Checkout\Model\Session');

        try{
            $item = $cart->getQuote()->getItemById($itemId);
            if (!$item) {
                \Mage::throwException(
                    __("The requested cart item doesn\'t exist.")
                );
            }

            $productId  = $item->getProductId();
            $buyRequest = $item->getBuyRequest();

            $wishlist->addNewItem($productId, $buyRequest);

            $productIds[] = $productId;
            $cart->getQuote()->removeItem($itemId);
            $cart->save();
            \Mage::helper('Magento\Wishlist\Helper\Data')->calculate();
            $productName = \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($item->getProduct()->getName());
            $wishlistName = \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($wishlist->getName());
            $session->addSuccess(
                __("%1 has been moved to wish list %2", $productName, $wishlistName)
            );
            $wishlist->save();
        } catch (\Magento\Core\Exception $e) {
            $session->addError($e->getMessage());
        } catch (\Exception $e) {
            $session->addException($e, __('We can\'t move the item to the wish list.'));
        }

        return $this->_redirectUrl(\Mage::helper('Magento\Checkout\Helper\Cart')->getCartUrl());
    }

    /**
     * Prepare wishlist for share
     */
    public function shareAction()
    {
        $this->_getWishlist();
        $this->loadLayout();
        $this->_initLayoutMessages('\Magento\Customer\Model\Session');
        $this->_initLayoutMessages('Magento_Wishlist_Model_Session');
        $this->renderLayout();
    }

    /**
     * Share wishlist
     *
     * @return \Magento\Core\Controller\Varien\Action|void
     */
    public function sendAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }

        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return $this->norouteAction();
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();
        $emails  = explode(',', $this->getRequest()->getPost('emails'));
        $error   = false;
        $message = (string) $this->getRequest()->getPost('message');
        if (strlen($message) > $textLimit) {
            $error = __('Message length must not exceed %1 symbols', $textLimit);
        } else {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emails)) {
                $error = __('Email address can\'t be empty.');
            } else if (count($emails) > $emailsLeft) {
                $error = __('This wishlist can be shared %1 more times.', $emailsLeft);
            } else {
                foreach ($emails as $index => $email) {
                    $email = trim($email);
                    if (!Zend_Validate::is($email, 'EmailAddress')) {
                        $error = __('Please input a valid email address.');
                        break;
                    }
                    $emails[$index] = $email;
                }
            }
        }

        if ($error) {
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->addError($error);
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
            return;
        }

        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        /* @var $translate \Magento\Core\Model\Translate */
        $translate->setTranslateInline(false);
        $sent = 0;

        try {
            $customer = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();

            /*if share rss added rss feed to email template*/
            if ($this->getRequest()->getParam('rss_url')) {
                $rss_url = $this->getLayout()
                    ->createBlock('\Magento\Wishlist\Block\Share\Email\Rss')
                    ->setWishlistId($wishlist->getId())
                    ->toHtml();
                $message .= $rss_url;
            }
            $wishlistBlock = $this->getLayout()->createBlock('\Magento\Wishlist\Block\Share\Email\Items')->toHtml();

            $emails = array_unique($emails);
            /* @var $emailModel \Magento\Core\Model\Email\Template */
            $emailModel = \Mage::getModel('\Magento\Core\Model\Email\Template');

            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach($emails as $email) {
                    $emailModel->sendTransactional(
                        \Mage::getStoreConfig('wishlist/email/email_template'),
                        \Mage::getStoreConfig('wishlist/email/email_identity'),
                        $email,
                        null,
                        array(
                            'customer'      => $customer,
                            'salable'       => $wishlist->isSalable() ? 'yes' : '',
                            'items'         => $wishlistBlock,
                            'addAllLink'    => \Mage::getUrl('*/shared/allcart', array('code' => $sharingCode)),
                            'viewOnSiteLink'=> \Mage::getUrl('*/shared/index', array('code' => $sharingCode)),
                            'message'       => $message
                        )
                    );
                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $translate->setTranslateInline(true);

            $this->_eventManager->dispatch('wishlist_share', array('wishlist'=>$wishlist));
            \Mage::getSingleton('Magento\Customer\Model\Session')->addSuccess(
                __('Your wish list has been shared.')
            );
            $this->_redirect('*/*', array('wishlist_id' => $wishlist->getId()));
        } catch (\Exception $e) {
            $translate->setTranslateInline(true);
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->addError($e->getMessage());
            \Mage::getSingleton('Magento_Wishlist_Model_Session')->setSharingForm($this->getRequest()->getPost());
            $this->_redirect('*/*/share');
        }
    }

    /**
     * Custom options download action
     * @return void
     */
    public function downloadCustomOptionAction()
    {
        $option = \Mage::getModel('\Magento\Wishlist\Model\Item\Option')->load($this->getRequest()->getParam('id'));

        if (!$option->getId()) {
            return $this->_forward('noRoute');
        }

        $optionId = null;
        if (strpos($option->getCode(), \Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX) === 0) {
            $optionId = str_replace(\Magento\Catalog\Model\Product\Type\AbstractType::OPTION_PREFIX, '', $option->getCode());
            if ((int)$optionId != $optionId) {
                return $this->_forward('noRoute');
            }
        }
        $productOption = \Mage::getModel('\Magento\Catalog\Model\Product\Option')->load($optionId);

        if (!$productOption
            || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId()
            || $productOption->getType() != 'file'
        ) {
            return $this->_forward('noRoute');
        }

        try {
            $info      = unserialize($option->getValue());
            $filePath  = \Mage::getBaseDir() . $info['quote_path'];
            $secretKey = $this->getRequest()->getParam('key');

            if ($secretKey == $info['secret_key']) {
                $this->_prepareDownloadResponse($info['title'], array(
                    'value' => $filePath,
                    'type'  => 'filename'
                ));
            }

        } catch(\Exception $e) {
            $this->_forward('noRoute');
        }
        exit(0);
    }
}
