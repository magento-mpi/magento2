<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller;

use Magento\Sales\Model\Order;

/**
 * Paypal Standard Checkout Controller
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Standard extends \Magento\App\Action\Action
{
    /**
     * Order instance
     *
     * @var Order
     */
    protected $_order;

    /**
     * Get order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Send expire header to ajax response
     *
     * @return void
     */
    protected function _expireAjax()
    {
        if (!$this->_objectManager->get('Magento\Checkout\Model\Session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with paypal strandard order transaction information
     *
     * @return \Magento\Paypal\Model\Standard
     */
    public function getStandard()
    {
        return $this->_objectManager->get('Magento\Paypal\Model\Standard');
    }

    /**
     * When a customer chooses Paypal on Checkout/Payment page
     *
     * @return void
     */
    public function redirectAction()
    {
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $session->setPaypalStandardQuoteId($session->getQuoteId());
        $this->_view->loadLayout(false)->renderLayout();
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * When a customer cancel payment from paypal.
     *
     * @return void
     */
    public function cancelAction()
    {
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));

        if ($session->getLastRealOrderId()) {
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')
                ->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $this->_objectManager->get('Magento\Event\ManagerInterface')->dispatch(
                    'paypal_payment_cancel',
                    array(
                        'order' => $order,
                        'quote' => $session->getQuote()
                ));
                $order->cancel()->save();
            }
            $this->_objectManager->get('Magento\Paypal\Helper\Checkout')->restoreQuote();
        }
        $this->_redirect('checkout/cart');
    }

    /**
     * When paypal returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     *
     * @return void
     */
    public function successAction()
    {
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
}
