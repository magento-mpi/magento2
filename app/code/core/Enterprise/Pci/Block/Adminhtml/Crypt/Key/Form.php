<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Pci
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Encryption key change form block
 *
 */
class Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add form fields
     *
     * @return Enterprise_Pci_Block_Adminhtml_Crypt_Key_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $fieldset = $form->addFieldset('main_fieldset', array('legend' => Mage::helper('enterprise_pci')->__('New encryption key')));
        $fieldset->addField('generate_random', 'checkbox', array(
            'name'    => 'generate_random',
            'label'   => Mage::helper('enterprise_pci')->__('Auto-generate a key'),
            'value'   => 1,
            'onclick' => "$('crypt_key').disabled = this.checked;",
        ));
        $fieldset->addField('crypt_key', 'text', array(
            'name'      => 'crypt_key',
            'label'     => Mage::helper('enterprise_pci')->__('Or enter your own key'),
            'style'     => 'width:32em;',
            'maxlength' => 32,
            'after_element_html' => '<p>'
                . Mage::helper('enterprise_pci')->__('Magento uses this key to encrypt passwords, credit cards and more. System can auto-generate an encryption key for you and will display it on the next page.')
                . '</p>',
        ));
        $form->setUseContainer(true);
        if ($data = $this->getFormData()) {
            $form->addValues($data);
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
