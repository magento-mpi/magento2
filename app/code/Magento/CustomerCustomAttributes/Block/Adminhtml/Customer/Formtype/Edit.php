<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Create New Form Type Block
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Formtype;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    /**
     * Initialize Form Container
     *
     */
    protected function _construct()
    {
        $this->_objectId   = 'type_id';
        $this->_blockGroup = 'Magento_CustomerCustomAttributes';
        $this->_controller = 'adminhtml_customer_formtype';

        parent::_construct();

        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            $this->_updateButton('save', 'onclick', 'formType.save(false)');
            $this->_updateButton('save', 'data_attribute', null);
            $this->_addButton('save_and_edit_button', array(
                'label'     => __('Save and Continue Edit'),
                'onclick'   => 'formType.save(true)',
                'class'     => 'save'
            ));

            if ($this->_getFormType()->getIsSystem()) {
                $this->_removeButton('delete');
            }

            $this->_headerText = __('Edit Form Type "%1"', $this->_getFormType()->getCode());
        } else {
            $this->_headerText = __('New Form Type');
        }
    }
}
