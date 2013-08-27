<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Encryption key change form block
 *
 */
class Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Add form fields
     *
     * @return Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form
     */
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $fieldset = $form->addFieldset('main_fieldset', array('legend' => __('New Encryption Key')));
        $fieldset->addField('enc_key_note', 'note', array(
            'text' => __('The encryption key is used to protect passwords and other sensitive data.')
        ));
        $fieldset->addField('generate_random', 'select', array(
            'name'    => 'generate_random',
            'label'   => __('Auto-generate a Key'),
            'options' => array(
                0 => __('No'),
                1 => __('Yes'),
            ),
            'onclick' => "var cryptKey = $('crypt_key'); cryptKey.disabled = this.value == 1; if (cryptKey.disabled) {cryptKey.parentNode.parentNode.hide();} else {cryptKey.parentNode.parentNode.show();}",
            'note'    => __('The generated key will be displayed after changing.'),
        ));
        $fieldset->addField('crypt_key', 'text', array(
            'name'      => 'crypt_key',
            'label'     => __('New Key'),
            'style'     => 'width:32em;',
            'maxlength' => 32,
        ));
        $form->setUseContainer(true);
        if ($data = $this->getFormData()) {
            $form->addValues($data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
