<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Api_Buttons extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'api/userinfo.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('backButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Back'),
            'onclick'   => 'window.location.href=\''.$this->getUrl('*/*/').'\'',
            'class' => 'back'
        ));

        $this->addChild('resetButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('saveButton', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_User_Helper_Data')->__('Save Role'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#role-edit-form')
                )
            )
        ));

        $this->addChild('deleteButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Magento_Adminhtml_Helper_Data')->__('Delete Role'),
            'onclick'   => 'deleteConfirm(\'' . Mage::helper('Magento_Adminhtml_Helper_Data')->__('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', array('rid' => $this->getRequest()->getParam('rid'))) . '\')',
            'class' => 'delete'
        ));
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if( intval($this->getRequest()->getParam('rid')) == 0 ) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
