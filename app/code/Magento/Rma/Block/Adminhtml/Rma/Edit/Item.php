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
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Item extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData;

    /**
     * @var Magento_Rma_Model_Item_FormFactory
     */
    protected $_itemFormFactory;

    /**
     * @var Magento_Sales_Model_Order_ItemFactory
     */
    protected $_itemFactory;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Rma_Model_Item_FormFactory $itemFormFactory
     * @param Magento_Sales_Model_Order_ItemFactory $itemFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Rma_Model_Item_FormFactory $itemFormFactory,
        Magento_Sales_Model_Order_ItemFactory $itemFactory,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_itemFormFactory = $itemFormFactory;
        $this->_itemFactory = $itemFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Preparing form - container, which contains all attributes
     *
     * @return Magento_Rma_Block_Adminhtml_Rma_Edit_Item
     */
    public function initForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_rma');
        $form->setFieldNameSuffix();

        $item = $this->_coreRegistry->registry('current_rma_item');

        if (!$item->getId()) {
            // for creating RMA process when we have no item loaded, $item is just empty model
            $this->_populateItemWithProductData($item);
        }

        /* @var $customerForm Magento_Customer_Model_Form */
        $customerForm = $this->_formFactory->create();
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
            /** @var $orderItem Magento_Sales_Model_Order_Item */
            $orderItem = $this->_itemFactory->create()->load($this->getProductId());
            if ($orderItem && $orderItem->getId()) {
                $item->setProductAdminName($this->_rmaData->getAdminProductName($orderItem));
            }
        }
    }
}
