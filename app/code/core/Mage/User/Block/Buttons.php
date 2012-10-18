<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Block_Buttons extends Mage_Backend_Block_Template
{

    protected function _prepareLayout()
    {
        $this->addChild('backButton', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_User_Helper_Data')->__('Back'),
            'onclick'   => 'window.location.href=\''.$this->getUrl('*/*/').'\'',
            'class' => 'back'
        ));

        $this->addChild('resetButton', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_User_Helper_Data')->__('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('saveButton', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_User_Helper_Data')->__('Save Role'),
            //'onclick'   => 'roleForm.submit();return false;',
            'class' => 'save',
            'data_attr'  => array(
                'widget-button' => array('event' => 'save', 'related' => '#role_edit_form')
            )
        ));

        $this->addChild('deleteButton', 'Mage_Backend_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_User_Helper_Data')->__('Delete Role'),
            'onclick'   => 'deleteConfirm(\''
                . Mage::helper('Mage_User_Helper_Data')->__('Are you sure you want to do this?')
                . '\', \''
                . $this->getUrl('*/*/delete', array('rid' => $this->getRequest()->getParam('rid')))
                . '\')',
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
        if (intval($this->getRequest()->getParam('rid')) == 0 ) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
