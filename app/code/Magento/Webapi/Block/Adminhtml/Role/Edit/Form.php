<?php
/**
 * Web API Role edit form.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit_Form extends Magento_Backend_Block_Widget_Form
{
    /**
     * Prepare form container.
     *
     * @return Magento_Webapi_Block_Adminhtml_Role_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array(
            'action' => $this->getUrl('*/*/save'),
            'id' => 'edit_form',
            'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
