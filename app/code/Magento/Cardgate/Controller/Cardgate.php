<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cardgate Controller
 *
 * @category   Magento
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Controller_Cardgate extends Magento_Core_Controller_Front_Action
{
    /**
     * Card Gate Base Object
     *
     * @var Magento_Cardgate_Model_Base
     */
    protected $_base;

    /**
     * Card Gate Base Object
     *
     * @var Magento_Cardgate_Model_BaseFactory
     */
    protected $_baseFactory;

    /**
     * Checkout Session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Sales Order
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_salesOrder;

    /**
     * Registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * Card Gate Gateway Model
     *
     * @var string
     */
    protected $_gatewayModel;

    /**
     * Constructor
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Cardgate_Model_Base $base
     * @param Magento_Cardgate_Model_BaseFactory $baseFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Sales_Model_Order $salesOrder
     * @param Magento_Core_Model_Registry $registry
     * @param string $areaCode
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Cardgate_Model_Base $base,
        Magento_Cardgate_Model_BaseFactory $baseFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Sales_Model_Order $salesOrder,
        Magento_Core_Model_Registry $registry,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);

        $this->_base = $base;
        $this->_baseFactory = $baseFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_salesOrder = $salesOrder;
        $this->_registry = $registry;
    }

    /**
     * Verify the callback
     *
     * @param array $data
     * @return boolean
     */
    protected function validate($data)
    {
        $hashString = ($this->_base->isTest() ? 'TEST' : '') .
            $data['transaction_id'] .
            $data['currency'] .
            $data['amount'] .
            $data['ref'] .
            $data['status'] .
            $this->_base->getConfigData('hash_key');

        if (md5($hashString) == $data['hash']) {
            return true;
        }

        return false;
    }

    /**
     * Check if within the URL in model param
     * if not, return default gateway model
     *
     * @return string|null
     */
    protected function getGatewayModel()
    {
        if ($this->_gatewayModel) {
            return $this->_gatewayModel;
        }

        $model = $this->getRequest()->getParam('model');
        $model = preg_replace('/[^[[:alnum:]]]+/', '', $model);

        return !empty($model) ? $model : null;
    }

    /**
     * Redirect customer to the gateway using his preferred payment method
     */
    public function redirectAction()
    {
        $gatewayModel = $this->getGatewayModel();
        if (!$gatewayModel) {
            return;
        }
        $this->_registry->register('cardgate_model', $gatewayModel);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * After a failed transaction a customer will be send here
     */
    public function cancelAction()
    {
        $orderId = $this->_checkoutSession->getLastRealOrderId();
        $order = $this->_salesOrder->loadByIncrementId($orderId);
        if ($orderId) {
            $order->setState($this->_base->getConfigData('order_status_failed'));
            $order->cancel();
            $order->save();
        }

        $quote = $this->_checkoutSession->getQuote();
        if ($quote->getId()) {
            $quote->setIsActive(true);
            $quote->save();
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * After a successful transaction a customer will be send here
     */
    public function successAction()
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote->getId()) {
            $quote->setIsActive(false);
            $quote->delete();
        }

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
     * Control URL called by gateway
     */
    public function controlAction()
    {
        /** @var Magento_Cardgate_Model_Base $base */
        $base = $this->_baseFactory->create();
        $data = $this->getRequest()->getPost();

        // Verify callback hash
        if (!$this->getRequest()->isPost() || !$this->validate($data)) {
            $base->log('Callback hash validation failed!');
            $this->getResponse()->setBody('');
            return;
        }

        // Log Callback data
        $base->log('Receiving callback data:');
        $base->log($data);

        try {
            // Process callback
            $base->setCallbackData($data)->processCallback();
        } catch (RuntimeException $e) {
            $base->log($e->getMessage());
            $this->getResponse()->setBody('');
            return;
        }

        // Display transaction_id and status
        $this->getResponse()->setBody($data['transaction_id'] . '.' . $data['status']);
    }
}
