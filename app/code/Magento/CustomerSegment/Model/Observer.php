<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CustomerSegment observer
 */
namespace Magento\CustomerSegment\Model;

class Observer
{
    /**
     * @var \Magento\CustomerSegment\Helper\Data
     */
    private $_segmentHelper;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Backend\Model\Config\Source\Yesno
     */
    protected $_configSourceYesno;

    /**
     * @var \Magento\CustomerSegment\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store list manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\CustomerSegment\Model\Customer $customer
     * @param \Magento\Backend\Model\Config\Source\Yesno $configSourceYesno
     * @param \Magento\CustomerSegment\Helper\Data $segmentHelper
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CustomerSegment\Model\Customer $customer,
        \Magento\Backend\Model\Config\Source\Yesno $configSourceYesno,
        \Magento\CustomerSegment\Helper\Data $segmentHelper,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
        $this->_configSourceYesno = $configSourceYesno;
        $this->_coreRegistry = $coreRegistry;
        $this->_segmentHelper = $segmentHelper;
    }

    /**
     * Add Customer Segment condition to the salesrule management
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addSegmentsToSalesRuleCombine(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        $additional = $observer->getEvent()->getAdditional();
        $additional->setConditions(array(array(
            'label' => __('Customer Segment'),
            'value' => 'Magento\CustomerSegment\Model\Segment\Condition\Segment'
        )));
    }

    /**
     * Process customer related data changing. Method can process just events with customer object
     *
     * @param   \Magento\Event\Observer $observer
     */
    public function processCustomerEvent(\Magento\Event\Observer $observer)
    {
        $customer  = $observer->getEvent()->getCustomer();
        $dataObject= $observer->getEvent()->getDataObject();

        $customerId= false;
        if ($customer) {
            $customerId = $customer->getId();
        }
        if (!$customerId && $dataObject) {
            $customerId = $dataObject->getCustomerId();
        }
        if ($customerId) {
            $this->_customer->processCustomerEvent($observer->getEvent()->getName(), $customerId);
        }
    }

    /**
     * Match customer segments on supplied event for currently logged in customer or visitor and current website.
     * Can be used for processing just frontend events
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processEvent(\Magento\Event\Observer $observer)
    {
        $customer = $this->_coreRegistry->registry('segment_customer');

        // For visitors use customer instance from customer session
        if (!$customer) {
            $customer = $this->_customerSession->getCustomer();
        }

        $this->_customer->processEvent($observer->getEvent()->getName(), $customer,
            $this->_storeManager->getStore()->getWebsite());
    }

    /**
     * Match quote customer to all customer segments.
     * Used before quote recollect in admin
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function processQuote(\Magento\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();
        if ($customer && $customer->getId()) {
            $website = $quote->getStore()->getWebsite();
            $this->_customer->processCustomer($customer, $website);
        }
    }

    /**
     * Add field "Use in Customer Segment" for Customer and Customer Address attribute edit form
     *
     * @param \Magento\Event\Observer $observer
     */
    public function enterpiseCustomerAttributeEditPrepareForm(\Magento\Event\Observer $observer)
    {
        $form       = $observer->getEvent()->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        $fieldset->addField('is_used_for_customer_segment', 'select', array(
            'name'      => 'is_used_for_customer_segment',
            'label'     => __('Use in Customer Segment'),
            'title'     => __('Use in Customer Segment'),
            'values'    => $this->_configSourceYesno->toOptionArray(),
        ));
    }

    /**
     * Add Customer Segment form fields to Target Rule form
     *
     * Observe  targetrule_edit_tab_main_after_prepare_form event
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addFieldsToTargetRuleForm(\Magento\Event\Observer $observer)
    {
        if (!$this->_segmentHelper->isEnabled()) {
            return;
        }
        /* @var $form \Magento\Data\Form */
        $form = $observer->getEvent()->getForm();
        /** @var \Magento\Object $model */
        $model = $observer->getEvent()->getModel();
        /** @var \Magento\Core\Block\AbstractBlock $block */
        $block = $observer->getEvent()->getBlock();

        /** @var \Magento\Backend\Block\Widget\Form\Element\Dependence $fieldDependencies */
        $fieldDependencies = $block->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $block->setChild('form_after', $fieldDependencies);

        $this->_segmentHelper->addSegmentFieldsToForm($form, $model, $fieldDependencies);
    }
}
