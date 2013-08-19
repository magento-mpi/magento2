<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorize Payment Controller
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paygate_Controller_Authorizenet_Payment extends Mage_Core_Controller_Front_Action
{

    /**
     * Cancel active partail authorizations
     */
    public function cancelAction()
    {
        $result['success'] = false;
        try {
            $paymentMethod = Mage::helper('Mage_Payment_Helper_Data')
                ->getMethodInstance(Mage_Paygate_Model_Authorizenet::METHOD_CODE);
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization(Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote()->getPayment());
            }
            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $this->__('There was an error canceling transactions. Please contact us or try again later.');
        }

        Mage::getSingleton('Mage_Checkout_Model_Session')->getQuote()->getPayment()->save();
        $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result));
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
