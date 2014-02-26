<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RecurringProfile\Model;

/**
 * Recurring profile observer
 */
class Observer
{
    /**
     * Locale model
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Recurring profile factory
     *
     * @var \Magento\RecurringProfile\Model\RecurringProfileFactory
     */
    protected $_recurringProfileFactory;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\RecurringProfile\Model\QuoteImporter
     */
    protected $_quoteImporter;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\RecurringProfile\Model\RecurringProfileFactory $recurringProfileFactory
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param QuoteImporter $quoteImporter
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\RecurringProfile\Model\RecurringProfileFactory $recurringProfileFactory,
        \Magento\RecurringProfile\Block\Fields $fields,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\RecurringProfile\Model\QuoteImporter $quoteImporter
    ) {
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_recurringProfileFactory = $recurringProfileFactory;
        $this->_fields = $fields;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteImporter = $quoteImporter;
    }

    /**
     * Collect buy request and set it as custom option
     *
     * Also sets the collected information and schedule as informational static options
     *
     * @param \Magento\Event\Observer $observer
     */
    public function prepareProductRecurringProfileOptions($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $buyRequest = $observer->getEvent()->getBuyRequest();

        if (!$product->getIsRecurring()) {
            return;
        }

        /** @var \Magento\RecurringProfile\Model\RecurringProfile $profile */
        $profile = $this->_recurringProfileFactory->create(['locale' => $this->_locale]);
        $profile->setStore($this->_storeManager->getStore())
            ->importBuyRequest($buyRequest)
            ->importProduct($product);
        if (!$profile) {
            return;
        }

        // add the start datetime as product custom option
        $product->addCustomOption(\Magento\RecurringProfile\Model\RecurringProfile::PRODUCT_OPTIONS_KEY,
            serialize(array('start_datetime' => $profile->getStartDatetime()))
        );

        // duplicate as 'additional_options' to render with the product statically
        $infoOptions = array(array(
            'label' => $this->_fields->getFieldLabel('start_datetime'),
            'value' => $profile->exportStartDatetime(),
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
     * Submit recurring profiles
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function submitRecurringPaymentProfiles($observer)
    {
        $profiles = $this->_quoteImporter->import($observer->getEvent()->getQuote());
        foreach ($profiles as $profile) {
            if (!$profile->isValid()) {
                throw new \Magento\Core\Exception($profile->getValidationErrors());
            }
            $profile->submit();
        }
    }

    /**
     * Add recurring profile ids to session
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addRecurringProfileIdsToSession($observer)
    {
        $profiles = $this->_quoteImporter->import($observer->getEvent()->getQuote());
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
        }
    }

    /**
     * Unserialize product recurring profile
     *
     * @param \Magento\Event\Observer $observer
     */
    public function unserializeProductRecurringProfile($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $product) {
            if ($product->getIsRecurring() && $profile = $product->getRecurringProfile()) {
                $product->setRecurringProfile(unserialize($profile));
            }
        }
    }

    /**
     * Set recurring data to quote
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setIsRecurringToQuote($observer)
    {
        $quote = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        $quote->setIsRecurring($product->getIsRecurring());
    }

    /**
     * Add recurring profile field to excluded list
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addFormExcludedAttribute($observer)
    {
        $block = $observer->getEvent()->getObject();

        $block->setFormExcludedFieldList(array_merge($block->getFormExcludedFieldList(), ['recurring_profile']));
    }
}
