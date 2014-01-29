<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile observer
 */
namespace Magento\RecurringProfile\Model;

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
     * @var \Magento\Payment\Model\Recurring\ProfileFactory
     */
    protected $_profileFactory;

    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fields;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Payment\Model\Recurring\ProfileFactory $profileFactory
     * @param \Magento\View\Element\BlockFactory $blockFactory
     * @param \Magento\RecurringProfile\Block\Fields $fields
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Payment\Model\Recurring\ProfileFactory $profileFactory,
        \Magento\View\Element\BlockFactory $blockFactory,
        \Magento\RecurringProfile\Block\Fields $fields
    ) {
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_profileFactory = $profileFactory;
        $this->_blockFactory = $blockFactory;
        $this->_fields = $fields;
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

        if (!$product->isRecurring()) {
            return;
        }

        /** @var \Magento\Payment\Model\Recurring\Profile $profile */
        $profile = $this->_profileFactory->create(['locale' => $this->_locale]);
        $profile->setStore($this->_storeManager->getStore())
            ->importBuyRequest($buyRequest)
            ->importProduct($product);
        if (!$profile) {
            return;
        }

        // add the start datetime as product custom option
        $product->addCustomOption(\Magento\Payment\Model\Recurring\Profile::PRODUCT_OPTIONS_KEY,
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
     * Add the recurring profile form when editing a product
     *
     * @param \Magento\Event\Observer $observer
     */
    public function renderForm($observer)
    {
        // replace the element of recurring payment profile field with a form
        $profileElement = $observer->getEvent()->getProductElement();
        $product = $observer->getEvent()->getProduct();

        /** @var $formBlock \Magento\Sales\Block\Adminhtml\Recurring\Profile\Edit\Form */
        $formBlock = $this->_blockFactory->createBlock('Magento\Sales\Block\Adminhtml\Recurring\Profile\Edit\Form');
        $formBlock->setNameInLayout('adminhtml_recurring_profile_edit_form');
        $formBlock->setParentElement($profileElement);
        $formBlock->setProductEntity($product);
        $output = $formBlock->toHtml();

        // make the profile element dependent on is_recurring
        /** @var $dependencies \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $dependencies = $this->_blockFactory->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $dependencies->setNameInLayout('adminhtml_recurring_profile_edit_form_dependence');
        $dependencies->addFieldMap('is_recurring', 'product[is_recurring]');
        $dependencies->addFieldMap($profileElement->getHtmlId(), $profileElement->getName());
        $dependencies->addFieldDependence($profileElement->getName(), 'product[is_recurring]', '1');
        $dependencies->addConfigOptions(array('levels_up' => 2));

        $output .= $dependencies->toHtml();

        $observer->getEvent()->getResult()->output = $output;
    }
}
