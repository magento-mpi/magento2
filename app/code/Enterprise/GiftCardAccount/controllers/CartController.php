<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_CartController extends Mage_Core_Controller_Front_Action
{
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
                if (strlen($code) > Enterprise_GiftCardAccount_Helper_Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    Mage::throwException(Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Please correct the gift card code.'));
                }
                Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                Mage::getSingleton('Mage_Checkout_Model_Session')->addSuccess(
                    $this->__('Gift Card "%1" was added.', Mage::helper('Mage_Core_Helper_Data')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_eventManager->dispatch(
                    'enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code)
                );
                Mage::getSingleton('Mage_Checkout_Model_Session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Checkout_Model_Session')->addException($e, $this->__('We cannot apply this gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }

    public function removeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                Mage::getSingleton('Mage_Checkout_Model_Session')->addSuccess(
                    $this->__('Gift Card "%1" was removed.', Mage::helper('Mage_Core_Helper_Data')->escapeHtml($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('Mage_Checkout_Model_Session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Checkout_Model_Session')->addException($e, $this->__('We cannot remove this gift card.'));
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
        /* @var $card Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $card = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
            ->loadByCode($this->getRequest()->getParam('giftcard_code', ''));
        Mage::register('current_giftcardaccount', $card);
        try {
            $card->isValid(true, true, true, false);
        }
        catch (Mage_Core_Exception $e) {
            $card->unsetData();
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
