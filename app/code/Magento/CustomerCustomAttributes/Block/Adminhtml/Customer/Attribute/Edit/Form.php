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
 * Customer Attributes Edit container
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Edit_Form
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
