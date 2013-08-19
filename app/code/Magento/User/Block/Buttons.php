<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_User_Block_Buttons extends Magento_Backend_Block_Template
{

    protected function _prepareLayout()
    {
        $this->addChild('backButton', 'Magento_Backend_Block_Widget_Button', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\''.$this->getUrl('*/*/').'\'',
            'class' => 'back'
        ));

        $this->addChild('resetButton', 'Magento_Backend_Block_Widget_Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location.reload()'
        ));

        $this->addChild('saveButton', 'Magento_Backend_Block_Widget_Button', array(
            'label'     => __('Save Role'),
            'class' => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#role-edit-form'),
                ),
            ),
        ));

        $this->addChild('deleteButton', 'Magento_Backend_Block_Widget_Button', array(
            'label'     => __('Delete Role'),
            'onclick'   => 'deleteConfirm(\''
                . __('Are you sure you want to do this?')
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
