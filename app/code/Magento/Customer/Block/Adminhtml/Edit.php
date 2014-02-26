<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

/**
 * Customer edit block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
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

        if ($this->getCustomerId() && $this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->_addButton('order', array(
                'label' => __('Create Order'),
                'onclick' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\')',
                'class' => 'add',
            ), 0);
        }

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Customer'));
        $this->_updateButton('delete', 'label', __('Delete Customer'));

        $customer = $this->_coreRegistry->registry('current_customer');
        if ($customer && $this->_coreRegistry->registry('current_customer')->isReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        }

        if (!$customer || !$this->_coreRegistry->registry('current_customer')->isDeleteable()) {
            $this->_removeButton('delete');
        }

        if ($customer && $customer->getId()) {
            $url = $this->getUrl('customer/index/resetPassword', array('customer_id' => $customer->getId()));
            $this->_addButton('reset_password', array(
                'label' => __('Reset Password'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'save',
            ), 0);
        }
    }

    /**
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('sales/order_create/start', array('customer_id' => $this->getCustomerId()));
    }

    /**
     * @return int|false
     */
    public function getCustomerId()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return $customer ? $customer->getId() : false;
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_customer')->getId()) {
            return $this->escapeHtml($this->_coreRegistry->registry('current_customer')->getName());
        } else {
            return __('New Customer');
        }
    }

    /**
     * Prepare form html. Add block for composite product modification interface
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock('Magento\Catalog\Block\Adminhtml\Product\Composite\Configure')
            ->toHtml();
        return $html;
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('customer/*/validate', array('_current' => true));
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->_coreRegistry->registry('current_customer')->isReadonly()) {
            $this->_addButton('save_and_continue', array(
                'label'     => __('Save and Continue Edit'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
            ), 10);
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('customer/index/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ));
    }
}
