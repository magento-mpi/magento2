<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 */


/**
 * OAuth consumer edit form block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Api2_Block_Adminhtml_Attribute_Edit_Form
     */
    protected function _prepareForm()
    {
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));


        $form->setAction($this->getUrl('*/*/save', array('type' => $this->getRequest()->getParam('type'))))
            ->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
