<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Authorize Payment Controller
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Controller\Adminhtml\Authorizenet;

class Payment extends \Magento\Backend\App\Action
{
    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote
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
                ->getMethodInstance(\Magento\Authorizenet\Model\Authorizenet::METHOD_CODE);

            if ($paymentMethod) {
                $paymentMethod->setStore(
                    $this->_sessionQuote->getQuote()->getStoreId()
                );
                $paymentMethod->cancelPartialAuthorization(
                    $this->_sessionQuote->getQuote()->getPayment()
                );
            }

            $result['success']  = true;
            $result['update_html'] = $this->_objectManager->get('Magento\Authorizenet\Helper\Data')
                ->getPaymentMethodsHtml($this->_view);
        } catch (\Magento\Core\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $result['error_message'] = $e->getMessage();
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $result['error_message'] = __('Something went wrong canceling the transactions.');
        }

        $this->_sessionQuote->getQuote()->getPayment()->save();
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }
}
