<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorize Payment Controller
 *
 * @category   Magento
 * @package    Magento_Paygate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paygate_Controller_Authorizenet_Payment extends Magento_Core_Controller_Front_Action
{
    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_session;

    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Checkout_Model_Session $session
    ) {
        $this->_session = $session;
        parent::__construct($context);
    }


    /**
     * Cancel active partail authorizations
     */
    public function cancelAction()
    {
        $result['success'] = false;
        try {
            $paymentMethod = $this->_objectManager->get('Magento_Payment_Helper_Data')
                ->getMethodInstance(Magento_Paygate_Model_Authorizenet::METHOD_CODE);
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization(
                    $this->_session->getQuote()->getPayment()
                );
            }
            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Magento_Core_Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $result['error_message'] = __('There was an error canceling transactions. Please contact us or try again later.');
        }

        $this->_session->getQuote()->getPayment()->save();
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Get payment method step html
     *
     * @return string
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateElements();
        $output = $layout->getOutput();
        return $output;
    }
}
