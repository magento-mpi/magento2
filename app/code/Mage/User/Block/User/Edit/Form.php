<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions user edit form
 *
 * @category   Mage
 * @package    Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Block_User_Edit_Form extends Magento_Backend_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Magento_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
