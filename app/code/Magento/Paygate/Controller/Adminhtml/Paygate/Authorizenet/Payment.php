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
namespace Magento\Paygate\Controller\Adminhtml\Paygate\Authorizenet;

class Payment extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Session quote
     *
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote
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
            $paymentMethod = $this->_objectManager->get('Magento\Payment\Helper\Data')
                ->getMethodInstance(\Magento\Paygate\Model\Authorizenet::METHOD_CODE);

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
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $result['error_message'] = __('Something went wrong canceling the transactions.');
        }

        $this->_sessionQuote->getQuote()->getPayment()->save();
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
