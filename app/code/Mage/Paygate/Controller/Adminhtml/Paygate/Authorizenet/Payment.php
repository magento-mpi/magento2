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
class Mage_Paygate_Controller_Adminhtml_Paygate_Authorizenet_Payment extends Mage_Adminhtml_Controller_Action
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
                $paymentMethod->setStore(Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getQuote()->getStoreId());
                $paymentMethod->cancelPartialAuthorization(Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getQuote()->getPayment());
            }

            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error_message'] = $this->__('Something went wrong canceling the transactions.');
        }

        Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getQuote()->getPayment()->save();
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
