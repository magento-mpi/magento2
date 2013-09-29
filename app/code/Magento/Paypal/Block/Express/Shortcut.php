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
 * Paypal express checkout shortcut link
 */
class Magento_Paypal_Block_Express_Shortcut extends Magento_Core_Block_Template
{
    /**
     * Position of "OR" label against shortcut
     */
    const POSITION_BEFORE = 'before';
    const POSITION_AFTER = 'after';

    /**
     * Whether the block should be eventually rendered
     *
     * @var bool
     */
    protected $_shouldRender = true;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Start express action
     *
     * @var string
     */
    protected $_startAction = 'paypal/express/start';

    /**
     * Express checkout model factory name
     *
     * @var string
     */
    protected $_checkoutType = 'Magento_Paypal_Model_Express_Checkout';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData;

    /**
     * Paypal data
     *
     * @var Magento_Paypal_Helper_Data
     */
    protected $_paypalData;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Paypal_Model_ConfigFactory
     */
    protected $_paypalConfigFactory;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Paypal_Model_Express_Checkout_Factory
     */
    protected $_checkoutFactory;

    /**
     * @param Magento_Paypal_Helper_Data $paypalData
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Paypal_Model_ConfigFactory $paypalConfigFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory
     * @param array $data
     */
    public function __construct(
        Magento_Paypal_Helper_Data $paypalData,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Customer_Model_Session $customerSession,
        Magento_Paypal_Model_ConfigFactory $paypalConfigFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_paypalData = $paypalData;
        $this->_paymentData = $paymentData;
        $this->_locale = $locale;
        $this->_customerSession = $customerSession;
        $this->_paypalConfigFactory = $paypalConfigFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutFactory = $checkoutFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $result = parent::_beforeToHtml();
        $params = array($this->_paymentMethodCode);
        $config = $this->_paypalConfigFactory->create(array('params' => $params));
        $isInCatalog = $this->getIsInCatalogProduct();
        $quote = ($isInCatalog || '' == $this->getIsQuoteAllowed()) ? null : $this->_checkoutSession->getQuote();

        // check visibility on cart or product page
        $context = $isInCatalog ? 'visible_on_product' : 'visible_on_cart';
        if (!$config->$context) {
            $this->_shouldRender = false;
            return $result;
        }

        if ($isInCatalog) {
            // Show PayPal shortcut on a product view page only if product has nonzero price
            /** @var $currentProduct Magento_Catalog_Model_Product */
            $currentProduct = $this->_coreRegistry->registry('current_product');
            if (!is_null($currentProduct)) {
                $productPrice = (float)$currentProduct->getFinalPrice();
                if (empty($productPrice) && !$currentProduct->isGrouped()) {
                    $this->_shouldRender = false;
                    return $result;
                }
            }
        }
        // validate minimum quote amount and validate quote for zero grandtotal
        if (null !== $quote && (!$quote->validateMinimumAmount()
            || (!$quote->getGrandTotal() && !$quote->hasNominalItems()))) {
            $this->_shouldRender = false;
            return $result;
        }

        // check payment method availability
        $methodInstance = $this->_paymentData->getMethodInstance($this->_paymentMethodCode);
        if (!$methodInstance || !$methodInstance->isAvailable($quote)) {
            $this->_shouldRender = false;
            return $result;
        }

        // set misc data
        $this->setShortcutHtmlId($this->helper('Magento_Core_Helper_Data')->uniqHash('ec_shortcut_'))
            ->setCheckoutUrl($this->getUrl($this->_startAction))
        ;

        // use static image if in catalog
        if ($isInCatalog || null === $quote) {
            $this->setImageUrl($config->getExpressCheckoutShortcutImageUrl($this->_locale->getLocaleCode()));
        } else {
            $parameters = array(
                'params' => array(
                    'quote' => $quote,
                    'config' => $config,
                ),
            );
            $checkoutModel = $this->_checkoutFactory->create($this->_checkoutType, $parameters);
            $this->setImageUrl($checkoutModel->getCheckoutShortcutImageUrl());
        }

        // ask whether to create a billing agreement
        $customerId = $this->_customerSession->getCustomerId(); // potential issue for caching
        if ($this->_paypalData->shouldAskToCreateBillingAgreement($config, $customerId)) {
            $this->setConfirmationUrl($this->getUrl($this->_startAction,
                array(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT => 1)
            ));
            $this->setConfirmationMessage(__('Would you like to sign a billing agreement '
                . 'to streamline further purchases with PayPal?'));
        }

        return $result;
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_shouldRender) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Check is "OR" label position before shortcut
     *
     * @return bool
     */
    public function isOrPositionBefore()
    {
        return ($this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_BEFORE);

    }

    /**
     * Check is "OR" label position after shortcut
     *
     * @return bool
     */
    public function isOrPositionAfter()
    {
        return (!$this->getIsInCatalogProduct() && !$this->getShowOrPosition())
            || ($this->getShowOrPosition() && $this->getShowOrPosition() == self::POSITION_AFTER);
    }
}
