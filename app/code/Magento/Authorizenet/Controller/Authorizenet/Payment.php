<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Controller\Authorizenet;

class Payment extends \Magento\Framework\App\Action\Action
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Checkout\Model\Session $session)
    {
        $this->_session = $session;
        parent::__construct($context);
    }

    /**
     * Cancel active partial authorizations
     *
     * @return void
     */
    public function cancelAction()
    {
        $result['success'] = false;
        try {
            $paymentMethod = $this->_objectManager->get(
                'Magento\Payment\Helper\Data'
            )->getMethodInstance(
                \Magento\Authorizenet\Model\Authorizenet::METHOD_CODE
            );
            if ($paymentMethod) {
                $paymentMethod->cancelPartialAuthorization($this->_session->getQuote()->getPayment());
            }
            $result['success'] = true;
            $result['update_html'] = $this->_objectManager->get(
                'Magento\Authorizenet\Helper\Data'
            )->getPaymentMethodsHtml(
                $this->_view
            );
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $result['error_message'] = __(
                'There was an error canceling transactions. Please contact us or try again later.'
            );
        }

        $this->_session->getQuote()->getPayment()->save();
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
        );
    }
}
