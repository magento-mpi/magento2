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
 * Adminhtml product edit price block
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price extends Magento_Adminhtml_Block_Widget_Form
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

    protected function _prepareForm()
    {
        $product = $this->_coreRegistry->registry('product');

        $form = $this->_createForm();
        $fieldset = $form->addFieldset('tiered_price', array('legend' => __('Tier Pricing')));

        $fieldset->addField('default_price', 'label', array(
                'label'=> __('Default Price'),
                'title'=> __('Default Price'),
                'name'=>'default_price',
                'bold'=>true,
                'value'=>$product->getPrice()
        ));

        $fieldset->addField('tier_price', 'text', array(
                'name'=>'tier_price',
                'class'=>'requried-entry',
                'value'=>$product->getData('tier_price')
        ));

        $form->getElement('tier_price')->setRenderer(
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier')
        );

        $this->setForm($form);
    }
}// Class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price END
