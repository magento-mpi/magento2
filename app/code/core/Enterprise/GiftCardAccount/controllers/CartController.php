<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Cart "%s" was added successfully.', Mage::helper('core')->htmlEscape($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException(
                    $e,
                    $this->__('Can not apply Gift Cart, please try again later.')
                );
            }
        }
        $this->_redirect('checkout/cart');
    }

    public function removeAction()
    {
        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Cart "%s" was removed successfully.', Mage::helper('core')->htmlEscape($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException(
                    $e,
                    $this->__('Can not remove Gift Cart, please try again later.')
                );
            }
            $this->_redirect('checkout/cart');
        } else {
            $this->_forward('noRoute');
        }
    }

    /**
     * Check Gift Card expiration date and balance
     *
     */
    public function checkAction()
    {
        $data = $this->getRequest()->getPost();
        $card = null;
        if (isset($data['code'])) {
            $card = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')->loadByCode($data['code']);
            $website = Mage::app()->getWebsite()->getId();
            if ($card->getWebsiteId() != $website) {
                $card = new Varien_Object();
            }

        }
        Mage::register('current_giftcardaccount', $card);

        $this->loadLayout();
        $this->renderLayout();

    }
}