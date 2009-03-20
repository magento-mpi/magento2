<?php
class Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->addField('crypt_key', 'text', array(
            'name'     => 'crypt_key',
            'label'    => Mage::helper('enterprise_pci')->__('Set new encryption key'),
            'style'    => 'width:32em;',
            'maxlength' => 32,
        ));
        $form->addField('generate_random', 'checkbox', array(
            'name'  => 'generate_random',
            'label' => Mage::helper('enterprise_pci')->__('Auto-generate a key'),
            'value' => 1,
            'onclick' => "$('crypt_key').disabled = this.checked;",
        ));
        $form->setUseContainer(true);
        if ($data = $this->getFormData()) {
            $form->addValues($data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
