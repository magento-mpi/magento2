<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User-attributes block for RMA Item  in Admin RMA edit
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Item extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $formFactory, $data);
    }

    /**
     * Preparing form - container, which contains all attributes
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Item
     */
    public function initForm()
    {
        $form = $this->_createForm();
        $form->setHtmlIdPrefix('_rma');
        $form->setFieldNameSuffix();

        $item = $this->_coreRegistry->registry('current_rma_item');

        if (!$item->getId()) {
            // for creating RMA process when we have no item loaded, $item is just empty model
            $this->_populateItemWithProductData($item);
        }

        /* @var $customerForm Magento_Customer_Model_Form */
        $customerForm = Mage::getModel('Magento_Rma_Model_Item_Form');
        $customerForm->setEntity($item)
            ->setFormCode('default')
            ->initDefaultValues();

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>__('RMA Item Details'))
        );

        $fieldset->setProductName($this->escapeHtml($item->getProductAdminName()));
        $okButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('OK'),
                'class'   => 'ok_button',
            ));
        $fieldset->setOkButton($okButton->toHtml());

        $cancelButton = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Cancel'),
                'class'   => 'cancel_button',
            ));
        $fieldset->setCancelButton($cancelButton->toHtml());


        $attributes = $customerForm->getUserAttributes();

        foreach ($attributes as $attribute) {
            $attribute->unsIsVisible();
        }
        $this->_setFieldset($attributes, $fieldset);

        $form->setValues($item->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changin layout
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        Magento_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Widget_Form_Renderer_Element',
                $this->getNameInLayout() . '_element'
            )
        );
        Magento_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Magento_Rma_Block_Adminhtml_Rma_Edit_Item_Renderer_Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        Magento_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Magento_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Return predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'text' => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Text',
            'textarea' => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Textarea',
            'image' => 'Magento_Rma_Block_Adminhtml_Rma_Edit_Item_Form_Element_Image',
        );
    }

    /**
     * Add needed data (Product name) to RMA item during create process
     *
     * @param Magento_Rma_Model_Item $item
     */
    protected function _populateItemWithProductData($item)
    {
        if ($this->getProductId()) {
            $orderItem = Mage::getModel('Magento_Sales_Model_Order_Item')->load($this->getProductId());
            if ($orderItem && $orderItem->getId()) {
                $item->setProductAdminName(Mage::helper('Magento_Rma_Helper_Data')->getAdminProductName($orderItem));
            }
        }
    }
}
