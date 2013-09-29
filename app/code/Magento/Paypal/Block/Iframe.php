<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HSS iframe block
 */
class Magento_Paypal_Block_Iframe extends Magento_Payment_Block_Form
{
    /**
     * Whether the block should be eventually rendered
     *
     * @var bool
     */
    protected $_shouldRender = false;

    /**
     * Order object
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode;

    /**
     * Current iframe block instance
     *
     * @var Magento_Payment_Block_Form
     */
    protected $_block;

    /**
     * @var string
     */
    protected $_template = 'hss/js.phtml';

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $paymentCode = $this->_getCheckout()
            ->getQuote()
            ->getPayment()
            ->getMethod();
        if (in_array($paymentCode, $this->helper('Magento_Paypal_Helper_Hss')->getHssMethods())) {
            $this->_paymentMethodCode = $paymentCode;
            $templatePath = str_replace('_', '', $paymentCode);
            $templateFile = "{$templatePath}/iframe.phtml";
            if (file_exists($this->_viewFileSystem->getFilename($templateFile))) {
                $this->setTemplate($templateFile);
            } else {
                $this->setTemplate('hss/iframe.phtml');
            }
        }
    }

    /**
     * Get current block instance
     *
     * @return Magento_Paypal_Block_Iframe
     * @throws Magento_Core_Exception
     */
    protected function _getBlock()
    {
        if (!$this->_block) {
            $this->_block = $this->getLayout()
                ->createBlock('Magento_Paypal_Block_'
                    . str_replace(' ', '_', ucwords(str_replace('_', ' ', $this->_paymentMethodCode)))
                    . '_Iframe');
            if (!$this->_block instanceof Magento_Paypal_Block_Iframe) {
                throw new Magento_Core_Exception('Invalid block type');
            }
        }

        return $this->_block;
    }

    /**
     * Get order object
     *
     * @return Magento_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Before rendering html, check if is block rendering needed
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        if ($this->_getOrder()->getId()
            && $this->_getOrder()->getQuoteId() == $this->_getCheckout()->getLastQuoteId()
            && $this->_paymentMethodCode
        ) {
            $this->_shouldRender = true;
        }

        if ($this->getGotoSection() || $this->getGotoSuccessPage()) {
            $this->_shouldRender = true;
        }

        return parent::_beforeToHtml();
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_isAfterPaymentSave()) {
            $this->setTemplate('hss/js.phtml');
            return parent::_toHtml();
        }
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check whether block is rendering after save payment
     *
     * @return bool
     */
    protected function _isAfterPaymentSave()
    {
        $quote = $this->_getCheckout()->getQuote();
        if ($quote->getPayment()->getMethod() == $this->_paymentMethodCode
            && $quote->getIsActive()
            && $this->getTemplate()
            && $this->getRequest()->getActionName() == 'savePayment'
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get iframe action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->_getBlock()->getFrameActionUrl();
    }

    /**
     * Get secure token
     *
     * @return string
     */
    public function getSecureToken()
    {
        return $this->_getBlock()->getSecureToken();
    }

    /**
     * Get secure token ID
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        return $this->_getBlock()->getSecureTokenId();
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->_getBlock()->getTransactionUrl();
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->_getBlock()->isTestMode();
    }
}
