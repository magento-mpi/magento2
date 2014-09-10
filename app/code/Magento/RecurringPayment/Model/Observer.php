<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model;

/**
 * Recurring payment observer
 */
class Observer
{
    /**
     * Locale model
     *
     * @var \Magento\Framework\LocaleInterface
     */
    protected $_locale;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Recurring payment factory
     *
     * @var \Magento\RecurringPayment\Model\RecurringPaymentFactory
     */
    protected $_recurringPaymentFactory;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fields;

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
    public function prepareProductRecurringPaymentOptions($observer)
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

    /**
     * Unserialize product recurring payment
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function unserializeProductRecurringPayment($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $product) {
            if ($product->getIsRecurring() && ($payment = $product->getRecurringPayment())) {
                $product->setRecurringPayment(unserialize($payment));
            }
        }
    }

    /**
     * Set recurring data to quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function setIsRecurringToQuote($observer)
    {
        $quote = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        $quote->setIsRecurring($product->getIsRecurring());
    }

    /**
     * Add recurring payment field to excluded list
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addFormExcludedAttribute($observer)
    {
        $block = $observer->getEvent()->getObject();

        $block->setFormExcludedFieldList(array_merge($block->getFormExcludedFieldList(), array('recurring_payment')));
    }

    /**
     * Set recurring payment renderer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function setFormRecurringElementRenderer($observer)
    {
        $form = $observer->getEvent()->getForm();

        $recurringPaymentElement = $form->getElement('recurring_payment');
        $recurringPaymentBlock = $observer->getEvent()->getLayout()->createBlock(
            'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring'
        );

        if ($recurringPaymentElement) {
            $recurringPaymentElement->setRenderer($recurringPaymentBlock);
        }
    }
}
