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
 * Product attributes tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Attribute_New_Product_Attributes
    extends Magento_Adminhtml_Block_Catalog_Form
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager_Proxy
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($formFactory, $coreData, $context, $data);
    }

    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        /**
         * Initialize product object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::registry('product'));

        $fieldset = $form->addFieldset('group_fields', array());

        $attributes = $this->getGroupAttributes();

        $this->_setFieldset($attributes, $fieldset, array('gallery'));

        $values = Mage::registry('product')->getData();
        /**
         * Set attribute default values for new product
         */
        if (!Mage::registry('product')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($values[$attribute->getAttributeCode()])) {
                    $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }

        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_prepare_form', array('form'=>$form));
        $form->addValues($values);
        $form->setFieldNameSuffix('product');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Price',
            'image'   => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Image',
            'boolean' => 'Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Boolean',
        );

        $response = new Magento_Object();
        $response->setTypes(array());
        $this->_eventManager->dispatch('adminhtml_catalog_product_edit_element_types', array('response'=>$response));

        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }

    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getForm()->getElement('group_fields')->getChildrenHtml();
    }
}
