<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Controller_Cart extends Magento_Core_Controller_Front_Action
{
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
     * No index action, forward to 404
     *
     */
    public function indexAction()
    {
        $this->_forward('noRoute');
    }

    /**
     * Add Gift Card to current quote
     *
     */
    public function addAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                if (strlen($code) > Magento_GiftCardAccount_Helper_Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    Mage::throwException(__('Please correct the gift card code.'));
                }
                Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                Mage::getSingleton('Magento_Checkout_Model_Session')->addSuccess(
                    __('Gift Card "%1" was added.', $this->_objectManager->get('Magento_Core_Helper_Data')->escapeHtml($code))
                );
            } catch (Magento_Core_Exception $e) {
                $this->_eventManager->dispatch(
                    'magento_giftcardaccount_add', array('status' => 'fail', 'code' => $code)
                );
                Mage::getSingleton('Magento_Checkout_Model_Session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Checkout_Model_Session')->addException($e, __('We cannot apply this gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }

    public function removeAction()
    {
        $code = $this->getRequest()->getParam('code');
        if ($code) {
            try {
                Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                Mage::getSingleton('Magento_Checkout_Model_Session')->addSuccess(
                    __('Gift Card "%1" was removed.', $this->_objectManager->get('Magento_Core_Helper_Data')->escapeHtml($code))
                );
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Checkout_Model_Session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Checkout_Model_Session')->addException($e, __('We cannot remove this gift card.'));
            }
            $this->_redirect('checkout/cart');
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Check a gift card account availability
     *
     */
    public function quickCheckAction()
    {
        /* @var $card Magento_GiftCardAccount_Model_Giftcardaccount */
        $card = Mage::getModel('Magento_GiftCardAccount_Model_Giftcardaccount')
            ->loadByCode($this->getRequest()->getParam('giftcard_code', ''));
        $this->_coreRegistry->register('current_giftcardaccount', $card);
        try {
            $card->isValid(true, true, true, false);
        } catch (Magento_Core_Exception $e) {
            $card->unsetData();
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
