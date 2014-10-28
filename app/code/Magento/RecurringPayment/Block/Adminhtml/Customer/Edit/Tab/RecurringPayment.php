<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\RecurringPayment\Block\Adminhtml\Payment\Grid as PaymentGrid;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Adminhtml customer recurring profiles tab
 */
class RecurringPayment extends PaymentGrid implements TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var int
     */
    protected $_currentCustomerId;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $paymentCollection
     * @param \Magento\RecurringPayment\Model\States $recurringStates
     * @param \Magento\RecurringPayment\Block\Fields $fields
     * @param \Magento\RecurringPayment\Model\Method\PaymentMethodsList $payments
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\RecurringPayment\Model\Resource\Payment\CollectionFactory $paymentCollection,
        \Magento\RecurringPayment\Model\States $recurringStates,
        \Magento\RecurringPayment\Block\Fields $fields,
        \Magento\RecurringPayment\Model\Method\PaymentMethodsList $payments,
        \Magento\Framework\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_currentCustomerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        parent::__construct($context, $backendHelper, $paymentCollection, $recurringStates, $fields, $payments, $data);
    }

    /**
     * Disable filters and paging
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_recurring_payment');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Recurring Billing Payments (beta)');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Recurring Billing Payments (beta)');
    }

    /**
     * Can show tab in tabs
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool)$this->_currentCustomerId;
    }

    /**
     * Tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        if (!$this->_currentCustomerId) {
            return $this;
        }

        $collection = $this->_paymentCollection->create()->addFieldToFilter('customer_id', $this->_currentCustomerId);

        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('payment_id', 'desc');
        }

        $this->setCollection($collection);

        return \Magento\Backend\Block\Widget\Grid\Extended::_prepareCollection();
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'orders';
    }

    /**
     * Return grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('sales/recurringPayment/customerGrid', array('_current' => true));
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
