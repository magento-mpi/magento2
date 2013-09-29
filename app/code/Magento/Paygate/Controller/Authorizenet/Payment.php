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
namespace Magento\Paygate\Controller\Authorizenet;

class Payment extends \Magento\Core\Controller\Front\Action
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Checkout\Model\Session $session
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
            $paymentMethod = $this->_objectManager->get('Magento\Payment\Helper\Data')
                ->getMethodInstance(\Magento\Paygate\Model\Authorizenet::METHOD_CODE);
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization(
                    $this->_session->getQuote()->getPayment()
                );
            }
            $result['success']  = true;
            $result['update_html'] = $this->_getPaymentMethodsHtml();
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $result['error_message'] = __('There was an error canceling transactions. Please contact us or try again later.');
        }

        $this->_session->getQuote()->getPayment()->save();
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
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
