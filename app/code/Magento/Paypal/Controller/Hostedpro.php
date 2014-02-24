<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Pro Checkout Controller
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Controller;

class Hostedpro extends \Magento\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(\Magento\App\Action\Context $context, \Magento\Checkout\Model\Session $session)
    {
        parent::__construct($context);
        $this->_session = $session;
    }

    /**
     * When a customer return to website from gateway.
     */
    public function returnAction()
    {
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');;
        //TODO: some actions with order
        if ($session->getLastRealOrderId()) {
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * When a customer cancel payment from gateway.
     */
    public function cancelAction()
    {
        $this->_view->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_view->getLayout()->getBlock('hosted.pro.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        //TODO: clarify return logic whether customer will be returned in iframe or in parent window
        $this->_view->renderLayout();
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return mixed
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        $helper = $this->_objectManager->get('Magento\Paypal\Helper\Checkout');
        $helper->cancelCurrentOrder($errorMsg);
        if ($this->_session->restoreQuote()) {
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }
}
