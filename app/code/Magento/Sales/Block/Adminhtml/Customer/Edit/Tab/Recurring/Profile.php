<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer recurring profiles tab
 */
namespace Magento\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring;

use Magento\Customer\Controller\Adminhtml\Index as CustomerController;

class Profile
    extends \Magento\Sales\Block\Adminhtml\Recurring\Profile\Grid
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Service\V1\Dto\Customer
     */
    protected $_currentCustomer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Url $urlModel
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Sales\Model\Resource\Recurring\Profile\CollectionFactory $profileCollection
     * @param \Magento\Sales\Model\Recurring\ProfileFactory $recurringProfile
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Customer\Service\V1\CustomerService $customerService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Sales\Model\Resource\Recurring\Profile\CollectionFactory $profileCollection,
        \Magento\Sales\Model\Recurring\ProfileFactory $recurringProfile,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Customer\Service\V1\CustomerService $customerService,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;

        // @todo remove usage of REGISTRY_CURRENT_CUSTOMER in advantage of REGISTRY_CURRENT_CUSTOMER_ID
        $currentCustomer = $this->_coreRegistry->registry(CustomerController::REGISTRY_CURRENT_CUSTOMER);
        if ($currentCustomer) {
            $currentCustomerId = $currentCustomer->getId();
        } else {
            $currentCustomerId = $this->_coreRegistry->registry(CustomerController::REGISTRY_CURRENT_CUSTOMER_ID);
        }

        if ($currentCustomerId) {
            $this->_currentCustomer = $customerService->getCustomer($currentCustomerId);
        }

        parent::__construct(
            $context,
            $urlModel,
            $backendHelper,
            $paymentData,
            $profileCollection,
            $recurringProfile,
            $data
        );
    }

    /**
     * Disable filters and paging
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_recurring_profile');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Recurring Billing Profiles (beta)');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Recurring Billing Profiles (beta)');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return (bool)$this->_currentCustomer;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare collection for grid
     *
     * @return \Magento\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring\Profile
     */
    protected function _prepareCollection()
    {
        if (!$this->_currentCustomer) {
            return $this;
        }

        $collection = $this->_profileCollection->create()
            ->addFieldToFilter('customer_id', $this->_currentCustomer->getCustomerId());

        if (!$this->getParam($this->getVarNameSort())) {
            $collection->setOrder('profile_id', 'desc');
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
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
        return $this->getUrl('sales/recurring_profile/customerGrid', array('_current' => true));
    }
}
