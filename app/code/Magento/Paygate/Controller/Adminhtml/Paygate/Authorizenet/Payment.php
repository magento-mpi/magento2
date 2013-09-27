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
class Magento_Paygate_Controller_Adminhtml_Paygate_Authorizenet_Payment extends Magento_Adminhtml_Controller_Action
{
    /**
     * Session quote
     *
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_sessionQuote;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote
    ) {
        $this->_sessionQuote = $sessionQuote;
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
                $paymentMethod->setStore(
                    $this->_sessionQuote->getQuote()->getStoreId()
                );
                $paymentMethod->cancelPartialAuthorization(
                    $this->_sessionQuote->getQuote()->getPayment()
                );
            }

            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Magento_Core_Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $result['error_message'] = __('Something went wrong canceling the transactions.');
        }

        $this->_sessionQuote->getQuote()->getPayment()->save();
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
