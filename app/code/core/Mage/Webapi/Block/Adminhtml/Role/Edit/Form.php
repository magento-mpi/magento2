<?php
/**
 * Web API Role edit form.
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare form container.
     *
     * @return Mage_Webapi_Block_Adminhtml_Role_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
