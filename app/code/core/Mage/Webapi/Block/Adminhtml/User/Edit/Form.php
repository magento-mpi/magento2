<?php
/**
 * Web API User edit form
 *
 * @copyright {}
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser() setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser() getApiUser()
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare Form
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit_Form
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
