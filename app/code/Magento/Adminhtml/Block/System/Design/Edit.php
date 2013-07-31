<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_System_Design_Edit extends Magento_Adminhtml_Block_Widget
{

    protected $_template = 'system/design/edit.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setId('design_edit');
    }

    protected function _prepareLayout()
    {
        $this->addChild('back_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
            'class' => 'back'
        ));

        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('Save'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#design-edit-form'),
                ),
            ),
        ));

        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Core_Helper_Data')->__('Delete'),
            'onclick'   => 'confirmSetLocation(\''.Mage::helper('Mage_Core_Helper_Data')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
            'class'  => 'delete'
        ));
        return parent::_prepareLayout();
    }

    public function getDesignChangeId()
    {
        return Mage::registry('design')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getHeader()
    {
        $header = '';
        if (Mage::registry('design')->getId()) {
            $header = Mage::helper('Mage_Core_Helper_Data')->__('Edit Design Change');
        } else {
            $header = Mage::helper('Mage_Core_Helper_Data')->__('New Store Design Change');
        }
        return $header;
    }
}
