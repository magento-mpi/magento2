<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Observer_Backend_RecurringProfile_FormRenderer
{
    /**
     * @var Magento_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param Magento_Core_Model_BlockFactory $blockFactory
     */
    public function __construct(Magento_Core_Model_BlockFactory $blockFactory)
    {
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Add the recurring profile form when editing a product
     *
     * @param Magento_Event_Observer $observer
     */
    public function render($observer)
    {
        // replace the element of recurring payment profile field with a form
        $profileElement = $observer->getEvent()->getProductElement();
        $product = $observer->getEvent()->getProduct();

        /** @var $formBlock Magento_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form */
        $formBlock = $this->_blockFactory->createBlock('Magento_Sales_Block_Adminhtml_Recurring_Profile_Edit_Form');
        $formBlock->setNameInLayout('adminhtml_recurring_profile_edit_form');
        $formBlock->setParentElement($profileElement);
        $formBlock->setProductEntity($product);
        $output = $formBlock->toHtml();

        // make the profile element dependent on is_recurring
        /** @var $dependencies Magento_Backend_Block_Widget_Form_Element_Dependence */
        $dependencies = $this->_blockFactory->createBlock('Magento_Backend_Block_Widget_Form_Element_Dependence');
        $dependencies->setNameInLayout('adminhtml_recurring_profile_edit_form_dependence');
        $dependencies->addFieldMap('is_recurring', 'product[is_recurring]');
        $dependencies->addFieldMap($profileElement->getHtmlId(), $profileElement->getName());
        $dependencies->addFieldDependence($profileElement->getName(), 'product[is_recurring]', '1');
        $dependencies->addConfigOptions(array('levels_up' => 2));

        $output .= $dependencies->toHtml();

        $observer->getEvent()->getResult()->output = $output;
    }
}
