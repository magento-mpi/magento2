<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Edit Form
 */
class Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Initialize theme form
     *
     * @return Magento_Theme_Block_Adminhtml_System_Design_Theme_Edit_Form|Magento_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
              'id'      => 'edit_form',
              'action'  => $this->getUrl('*/*/save'),
              'enctype' => 'multipart/form-data',
              'method'  => 'post'
         ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
