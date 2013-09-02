<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Index_Block_Adminhtml_Process_Edit_Form extends Magento_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = $this->_createForm(array('id' => 'edit_form', 'action' => $this->getActionUrl(), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getActionUrl()
    {
        return $this->getUrl('adminhtml/process/save');
    }
}
