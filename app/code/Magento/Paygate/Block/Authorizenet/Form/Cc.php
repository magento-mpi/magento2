<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Paygate_Block_Authorizenet_Form_Cc extends Magento_Payment_Block_Form
{
    protected $_template = 'Magento_Paygate::form/cc.phtml';

    /**
     * Retreive payment method form html
     *
     * @return string
     */
    public function getMethodFormBlock()
    {
        return $this->getLayout()->createBlock('Magento_Payment_Block_Form_Cc')
            ->setMethod($this->getMethod());
    }

    /**
     * Cards info block
     *
     * @return string
     */
    public function getCardsBlock()
    {
        return $this->getLayout()->createBlock('Magento_Paygate_Block_Authorizenet_Info_Cc')
            ->setMethod($this->getMethod())
            ->setInfo($this->getMethod()->getInfoInstance())
            ->setCheckoutProgressBlock(false)
            ->setHideTitle(true);
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
     * Return url to admin cancel controller from admin url model
     *
     * @return string
     */
    public function getAdminCancelUrl()
    {
        return $this->_urlBuilder->getUrl('adminhtml/paygate_authorizenet_payment/cancel');
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
    public function showNoticeMessage($message)
    {
        return $this->getLayout()->getMessagesBlock()
            ->addNotice(__($message))
            ->getGroupedHtml();
    }

    /**
     * Return partial authorization confirmation message and unset it in payment model
     *
     * @return string
     */
    public function getPartialAuthorizationConfirmationMessage()
    {
        $lastActionState = $this->getMethod()->getPartialAuthorizationLastActionState();
        if ($lastActionState == Magento_Paygate_Model_Authorizenet::PARTIAL_AUTH_LAST_SUCCESS) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
            return __('You don\'t have enough on your credit card to pay for this purchase. To complete your purchase, click "OK" and add a credit card to use for the balance. Otherwise, you can cancel the purchase and release the partial payment we are holding.');
        } elseif ($lastActionState == Magento_Paygate_Model_Authorizenet::PARTIAL_AUTH_LAST_DECLINED) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
            return __('Your credit card has been declined. You can click OK to add another credit card to complete your purchase. Or you can cancel this credit transaction and pay a different way.');
        }
        return false;
    }

    /**
     * Return partial authorization form message and unset it in payment model
     *
     * @return string
     */
    public function getPartialAuthorizationFormMessage()
    {
        $lastActionState = $this->getMethod()->getPartialAuthorizationLastActionState();
        $message = false;
        switch ($lastActionState) {
            case Magento_Paygate_Model_Authorizenet::PARTIAL_AUTH_ALL_CANCELED:
                $message = __('We canceled your payment and released any money we were holding.');
                break;
            case Magento_Paygate_Model_Authorizenet::PARTIAL_AUTH_CARDS_LIMIT_EXCEEDED:
                $message = __('You can\'t use any more credit cards for this payment, and you don\'t have enough to pay for this purchase. Sorry, but we\'ll have to cancel your transaction.');
                break;
            case Magento_Paygate_Model_Authorizenet::PARTIAL_AUTH_DATA_CHANGED:
                $message = __('Your order has not been placed, because the contents of the shopping cart and/or your address has been changed. Authorized amounts from your previous payment that were left pending are now released. Please go through the checkout process to purchase your cart contents.');
                break;
        }
        if ($message) {
            $this->getMethod()->unsetPartialAuthorizationLastActionState();
        }
        return $message;
    }

    /**
     * Return cancel confirmation message
     *
     * @return string
     */
    public function getCancelConfirmationMessage()
    {
        return __('Are you sure you want to cancel your payment? Click OK to cancel your payment and release the amount on hold. Click Cancel to enter another credit card and continue with your payment.');
    }

    /**
     * Return flag - is partial authorization process started
     *
     * @return string
     */
    public function isPartialAuthorization()
    {
        return $this->getMethod()->isPartialAuthorization();
    }

    /**
     * Return HTML content for creating admin panel`s button
     *
     * @return string
     */
    public function getCancelButtonHtml()
    {
        $cancelButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'id'      => 'payment_cancel',
                'label'   => __('Cancel'),
                'onclick' => 'cancelPaymentAuthorizations()'
            ));
        return $cancelButton->toHtml();
    }
}
