<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Api_Buttons extends Mage_Adminhtml_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('api/userinfo.phtml');
    }

    protected function _prepareLayout()
    {
        $this->addChild('backButton', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Back'),
            'onclick'   => 'window.location.href=\''.$this->getUrl('*/*/').'\'',
            'class' => 'back'
        ));

        $this->addChild('resetButton', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('saveButton', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save Role'),
            //'onclick'   => 'roleForm.submit();return false;',
            'class' => 'save',
            'data_attr'  => array(
                'widget-button' => array('event' => 'save', 'related' => '#role_edit_form')
            )
        ));

        $this->addChild('deleteButton', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Delete Role'),
            'onclick'   => 'deleteConfirm(\'' . Mage::helper('Mage_Adminhtml_Helper_Data')->__('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', array('rid' => $this->getRequest()->getParam('rid'))) . '\')',
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
