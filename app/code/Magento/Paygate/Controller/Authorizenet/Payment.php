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
                    Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->getPayment()
                );
            }
            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Magento_Core_Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error_message'] = __('There was an error canceling transactions. '
                . 'Please contact us or try again later.');
        }

        Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->getPayment()->save();
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
