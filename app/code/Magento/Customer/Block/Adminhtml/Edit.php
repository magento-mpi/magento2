<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Customer\Service\V1\CustomerServiceInterface;

/**
 * Customer edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Customer service
     *
     * @var CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_viewHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param CustomerServiceInterface $customerService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        CustomerServiceInterface $customerService,
        \Magento\Customer\Helper\View $viewHelper,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerService = $customerService;
        $this->_viewHelper = $viewHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_Customer';

        $customerId = $this->getCustomerId();

        if ($customerId && $this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->_addButton('order', [
                'label' => __('Create Order'),
                'onclick' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\')',
                'class' => 'add',
            ], 0);
        }

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Customer'));
        $this->_updateButton('delete', 'label', __('Delete Customer'));

        if ($customerId && $this->_customerService->isReadonly($customerId)) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

        if (!$customerId || !$this->_customerService->isDeleteable($customerId)) {
            $this->_removeButton('delete');
        }

        if ($customerId) {
            $url = $this->getUrl('customer/index/resetPassword', ['customer_id' => $customerId]);
            $this->_addButton('reset_password', [
                'label' => __('Reset Password'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'save',
            ], 0);
        }
    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('sales/order_create/start', ['customer_id' => $this->getCustomerId()]);
    }

    /**
     * Return the customer Id.
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = $this->_coreRegistry->registry(Index::REGISTRY_CURRENT_CUSTOMER_ID);
        return $customerId;
    }

    /**
     * Retrieve the header text, either the name of an existing customer or 'New Customer'.
     *
     * @return string
     */
    public function getHeaderText()
    {
        $customerId = $this->getCustomerId();
        if ($customerId) {
            $customerData = $this->_customerService->getCustomer($customerId);
            return $this->escapeHtml($this->_viewHelper->getCustomerName($customerData));
        } else {
            return __('New Customer');
        }
    }

    /**
     * Prepare form Html. Add block for configurable product modification interface.
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()
            ->createBlock('Magento\Catalog\Block\Adminhtml\Product\Composite\Configure')
            ->toHtml();
        return $html;
    }

    /**
     * Retrieve customer validation Url.
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('customer/*/validate', ['_current' => true]);
    }

    /**
     * Prepare the layout.
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $customerId = $this->getCustomerId();
        if ($customerId && !$this->_customerService->isReadonly($customerId)) {
            $this->_addButton('save_and_continue', [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ],
            ], 10);
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('customer/index/save', [
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ]);
    }
}
