<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment Observer
 */
class Magento_Payment_Model_Observer
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Locale model
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Recurring profile factory
     *
     * @var Magento_Payment_Model_Recurring_ProfileFactory
     */
    protected $_profileFactory;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Payment_Model_Recurring_ProfileFactory $profileFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Payment_Model_Recurring_ProfileFactory $profileFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_profileFactory = $profileFactory;
    }
    /**
     * Set forced canCreditmemo flag
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Payment_Model_Observer
     */
    public function salesOrderBeforeSave($observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getPayment()->getMethodInstance()->getCode() != 'free') {
            return $this;
        }

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->isCanceled() || $order->getState() === Magento_Sales_Model_Order::STATE_CLOSED) {
            return $this;
        }
        /**
         * Allow forced creditmemo just in case if it wasn't defined before
         */
        if (!$order->hasForcedCanCreditmemo()) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }

    /**
     * Collect buy request and set it as custom option
     *
     * Also sets the collected information and schedule as informational static options
     *
     * @param Magento_Event_Observer $observer
     */
    public function prepareProductRecurringProfileOptions($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $buyRequest = $observer->getEvent()->getBuyRequest();

        if (!$product->isRecurring()) {
            return;
        }

        $profile = $this->_profileFactory->create()
            ->setLocale($this->_locale)
            ->setStore($this->_storeManager->getStore())
            ->importBuyRequest($buyRequest)
            ->importProduct($product);
        if (!$profile) {
            return;
        }

        // add the start datetime as product custom option
        $product->addCustomOption(Magento_Payment_Model_Recurring_Profile::PRODUCT_OPTIONS_KEY,
            serialize(array('start_datetime' => $profile->getStartDatetime()))
        );

        // duplicate as 'additional_options' to render with the product statically
        $infoOptions = array(array(
            'label' => $profile->getFieldLabel('start_datetime'),
            'value' => $profile->exportStartDatetime(true),
        ));

        foreach ($profile->exportScheduleInfo() as $info) {
            $infoOptions[] = array(
                'label' => $info->getTitle(),
                'value' => $info->getSchedule(),
            );
        }
        $product->addCustomOption('additional_options', serialize($infoOptions));
    }

    /**
     * Sets current instructions for bank transfer account
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function beforeOrderPaymentSave(Magento_Event_Observer $observer)
    {
        /** @var Magento_Sales_Model_Order_Payment $payment */
        $payment = $observer->getEvent()->getPayment();
        if($payment->getMethod() === Magento_Payment_Model_Method_Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE) {
            $payment->setAdditionalInformation('instructions',
                $payment->getMethodInstance()->getInstructions());
        }
    }

    /**
     * @param Magento_Event_Observer $observer
     */
    public function updateOrderStatusForPaymentMethods(Magento_Event_Observer $observer)
    {
        if ($observer->getEvent()->getState() !== Magento_Sales_Model_Order::STATE_NEW) {
            return;
        }
        $status = $observer->getEvent()->getStatus();
        $defaultStatus = $this->_objectManager->get('Magento_Sales_Model_Order_Config')
            ->getStateDefaultStatus(Magento_Sales_Model_Order::STATE_NEW);
        $methods = $this->_objectManager->get('Magento_Payment_Model_Config')->getActiveMethods();
        foreach ($methods as $method) {
            if ($method->getConfigData('order_status') == $status) {
                $this->_objectManager->get('Magento_Core_Model_Resource_Config')
                    ->saveConfig('payment/' . $method->getCode() . '/order_status', $defaultStatus, 'default', 0);
            }
        }
    }
}
