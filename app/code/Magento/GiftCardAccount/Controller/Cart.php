<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Controller;

class Cart extends \Magento\Core\Controller\Front\Action
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
                if (strlen($code) > \Magento\GiftCardAccount\Helper\Data::GIFT_CARD_CODE_MAX_LENGTH) {
                    \Mage::throwException(__('Please correct the gift card code.'));
                }
                \Mage::getModel('\Magento\GiftCardAccount\Model\Giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addSuccess(
                    __('Gift Card "%1" was added.', \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_eventManager->dispatch(
                    'magento_giftcardaccount_add', array('status' => 'fail', 'code' => $code)
                );
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(
                    $e->getMessage()
                );
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addException($e, __('We cannot apply this gift card.'));
            }
        }
        $this->_redirect('checkout/cart');
    }

    public function removeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            try {
                \Mage::getModel('\Magento\GiftCardAccount\Model\Giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addSuccess(
                    __('Gift Card "%1" was removed.', \Mage::helper('Magento\Core\Helper\Data')->escapeHtml($code))
                );
            } catch (\Magento\Core\Exception $e) {
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addError(
                    $e->getMessage()
                );
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Checkout\Model\Session')->addException($e, __('We cannot remove this gift card.'));
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
        /* @var $card \Magento\GiftCardAccount\Model\Giftcardaccount */
        $card = \Mage::getModel('\Magento\GiftCardAccount\Model\Giftcardaccount')
            ->loadByCode($this->getRequest()->getParam('giftcard_code', ''));
        \Mage::register('current_giftcardaccount', $card);
        try {
            $card->isValid(true, true, true, false);
        }
        catch (\Magento\Core\Exception $e) {
            $card->unsetData();
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
