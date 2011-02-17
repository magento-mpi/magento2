<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Paygate_Block_Authorizenet_Form_Cc extends Mage_Payment_Block_Form
{
    /**
     * Set block template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paygate/form/cc.phtml');
    }

    /**
     * Retreive payment method form html
     *
     * @return string
     */
    public function getMethodFormBlock()
    {
        return $this->getLayout()->createBlock('payment/form_cc')
            ->setMethod($this->getMethod());
    }

    /**
     * Cards info block
     *
     * @return string
     */
    public function getCardsBlock()
    {
        return $this->getLayout()->createBlock('paygate/authorizenet_cards')
            ->setMethod($this->getMethod());
    }

    /**
     * Return url to cancel controller
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('paygate/authorizenet_payment/cancel');
    }

    /**
     * Return url to admin cancel controller
     *
     * @return string
     */
    public function getAdminCancelUrl()
    {
        return $this->getUrl('adminhtml/paygate_authorizenet_payment/cancel');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setChild('cards', $this->getCardsBlock());
        $this->setChild('method_form_block', $this->getMethodFormBlock());
        return parent::_toHtml();
    }

    /**
     * Get notice message
     *
     * @return string
     */
    public function showNoticeMessage()
    {
        return $this->getLayout()->getMessagesBlock()
            ->addNotice($this->__('Please enter another credit card number to complete your purchase.'))
            ->getGroupedHtml();
    }

    /**
     * Return partial authorization confirmation message and unset it in payment model
     *
     * @return string
     */
    public function getPartialAuthorizationConfirmationMessage()
    {
        $message = $this->getMethod()->getPartialAuthorizationConfirmationMessage();
        $this->getMethod()->unsetPartialAuthorizationConfirmationMessage();
        return $message;
    }

    /**
     * Return cancel confirmation message
     *
     * @return string
     */
    public function getCancelConfirmationMessage()
    {
        return $this->__('Are you sure you want to cancel your payment? Click Yes to cancel your payment and release the amount on hold. Click No to enter another credit card and continue with your payment.');
    }

    /**
     * Return flag - is partial authorization process started
     *
     * @return string
     */
    public function isItPartialAuthorization()
    {
        return $this->getMethod()->isItPartialAuthorization();
    }

    public function getCancelButtonHtml()
    {
        $cancelButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'      => 'payment_cancel',
                'label'   => Mage::helper('paygate')->__('Cancel'),
                'onclick' => 'cancelPaymentAuthorizations()'
            ));
        return $cancelButton->toHtml();
    }
}
