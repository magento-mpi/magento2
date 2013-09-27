<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Assign order status to order state form
 */
class Magento_Adminhtml_Block_Sales_Order_Status_Assign_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Sales_Model_Resource_Order_Status_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @param Magento_Sales_Model_Order_Config $orderConfig
     * @param Magento_Sales_Model_Resource_Order_Status_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Sales_Model_Order_Config $orderConfig,
        Magento_Sales_Model_Resource_Order_Status_CollectionFactory $collectionFactory,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_orderConfig = $orderConfig;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_status_state');
    }

    /**
     * Prepare form fields
     *
     * @return Magento_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'method'    => 'post',
            ))
        );

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => __('Assignment Information')
        ));

        $statuses = $this->_collectionFactory->create()->toOptionArray();
        array_unshift($statuses, array('value' => '', 'label' => ''));

        $states = $this->_orderConfig->getStates();
        $states = array_merge(array('' => ''), $states);

        $fieldset->addField('status', 'select',
            array(
                'name'      => 'status',
                'label'     => __('Order Status'),
                'class'     => 'required-entry',
                'values'    => $statuses,
                'required'  => true,
            )
        );

        $fieldset->addField('state', 'select',
            array(
                'name'      => 'state',
                'label'     => __('Order State'),
                'class'     => 'required-entry',
                'values'    => $states,
                'required'  => true,
            )
        );

        $fieldset->addField('is_default', 'checkbox',
            array(
                'name'      => 'is_default',
                'label'     => __('Use Order Status As Default'),
                'value'     => 1,
            )
        );


        $form->setAction($this->getUrl('*/sales_order_status/assignPost'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
