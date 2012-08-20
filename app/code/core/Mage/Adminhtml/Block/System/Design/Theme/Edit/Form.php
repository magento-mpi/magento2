<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Edit Form
 */
class Mage_Adminhtml_Block_System_Design_Theme_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Initialize theme form
     *
     * @return Mage_Adminhtml_Block_System_Design_Theme_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
              'id'     => 'edit_form',
              'action' => $this->getUrl('*/*/save'),
              'method' => 'post'
         ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
