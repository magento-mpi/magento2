<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

/**
 * Customer edit block
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /** @var CustomerAccountServiceInterface */
    protected $_customerAccountService;

    /**
     * Customer view helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_viewHelper;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerAccountServiceInterface $customerAccountService,
        \Magento\Customer\Helper\View $viewHelper,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_customerAccountService = $customerAccountService;
        $this->_viewHelper = $viewHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_Customer';

        $customerId = $this->getCustomerId();

        if ($customerId && $this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->buttonList->add(
                'order',
                array(
                    'label' => __('Create Order'),
                    'onclick' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\')',
                    'class' => 'add'
                ),
                0
            );
        }

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Customer'));
        $this->buttonList->update('delete', 'label', __('Delete Customer'));

        if ($customerId && !$this->_customerAccountService->canModify($customerId)) {
            $this->buttonList->remove('save');
            $this->buttonList->remove('reset');
        }

        if (!$customerId || !$this->_customerAccountService->canDelete($customerId)) {
            $this->buttonList->remove('delete');
        }

        if ($customerId) {
            $url = $this->getUrl('customer/index/resetPassword', array('customer_id' => $customerId));
            $this->buttonList->add(
                'reset_password',
                array(
                    'label' => __('Reset Password'),
                    'onclick' => 'setLocation(\'' . $url . '\')',
                    'class' => 'reset reset-password'
                ),
                0
            );
        }
    }

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('sales/order_create/start', array('customer_id' => $this->getCustomerId()));
    }

    /**
     * Return the customer Id.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
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
            $customerData = $this->_customerAccountService->getCustomer($customerId);
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
        $html .= $this->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Product\Composite\Configure'
        )->toHtml();
        return $html;
    }

    /**
     * Retrieve customer validation Url.
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('customer/*/validate', array('_current' => true));
    }

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $customerId = $this->getCustomerId();
        if (!$customerId || $this->_customerAccountService->canModify($customerId)) {
            $this->buttonList->add(
                'save_and_continue',
                array(
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form')
                        )
                    )
                ),
                10
            );
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
        return $this->getUrl(
            'customer/index/save',
            array('_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}')
        );
    }
}
