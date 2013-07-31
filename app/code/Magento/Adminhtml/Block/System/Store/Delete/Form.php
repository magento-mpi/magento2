<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml cms block edit form
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_System_Store_Delete_Form extends Magento_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('store_delete_form');
        $this->setTitle(Mage::helper('Mage_Cms_Helper_Data')->__('Block Information'));
    }

    protected function _prepareForm()
    {
        $dataObject = $this->getDataObject();

        $form = new Magento_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $form->setHtmlIdPrefix('store_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('Mage_Core_Helper_Data')->__('Backup Options'), 'class' => 'fieldset-wide'));

        $fieldset->addField('item_id', 'hidden', array(
            'name'  => 'item_id',
            'value' => $dataObject->getId(),
        ));

        $fieldset->addField('create_backup', 'select', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Create DB Backup'),
            'title'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Create DB Backup'),
            'name'      => 'create_backup',
            'options'   => array(
                '1' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Yes'),
                '0' => Mage::helper('Magento_Adminhtml_Helper_Data')->__('No'),
            ),
            'value'     => '1',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
