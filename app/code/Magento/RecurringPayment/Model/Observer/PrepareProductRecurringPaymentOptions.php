<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class PrepareProductRecurringPaymentOptions
{
    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

    /**
     * Recurring payment factory
     *
     * @var \Magento\RecurringPayment\Model\RecurringPaymentFactory
     */
    protected $_recurringPaymentFactory;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Locale model
     *
     * @var \Magento\Framework\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Framework\LocaleInterface $locale
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\RecurringPayment\Model\RecurringPaymentFactory  $recurringPaymentFactory
     * @param \Magento\RecurringPayment\Block\Fields $fields
     */
    public function __construct(
        \Magento\Framework\LocaleInterface $locale,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\RecurringPayment\Model\RecurringPaymentFactory $recurringPaymentFactory,
        \Magento\RecurringPayment\Block\Fields $fields
    ) {
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_recurringPaymentFactory = $recurringPaymentFactory;
        $this->_fields = $fields;
    }

    /**
     * Collect buy request and set it as custom option
     *
     * Also sets the collected information and schedule as informational static options
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $buyRequest = $observer->getEvent()->getBuyRequest();

        if (!$product->getIsRecurring()) {
            return;
        }

        /** @var \Magento\RecurringPayment\Model\RecurringPayment $payment */
        $payment = $this->_recurringPaymentFactory->create(array('locale' => $this->_locale));
        $payment->setStore($this->_storeManager->getStore())->importBuyRequest($buyRequest)->importProduct($product);
        if (!$payment) {
            return;
        }

        // add the start datetime as product custom option
        $product->addCustomOption(
            \Magento\RecurringPayment\Model\RecurringPayment::PRODUCT_OPTIONS_KEY,
            serialize(array('start_datetime' => $payment->getStartDatetime()))
        );

        // duplicate as 'additional_options' to render with the product statically
        $infoOptions = array(
            array(
                'label' => $this->_fields->getFieldLabel('start_datetime'),
                'value' => $payment->exportStartDatetime()
            )
        );

        foreach ($payment->exportScheduleInfo() as $info) {
            $infoOptions[] = array('label' => $info->getTitle(), 'value' => $info->getSchedule());
        }
        $product->addCustomOption('additional_options', serialize($infoOptions));
    }
}
