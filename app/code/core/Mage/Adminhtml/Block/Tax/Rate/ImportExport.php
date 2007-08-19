<?php

class Mage_Adminhtml_Block_Tax_Rate_ImportExport extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'tag_id';
        $this->_controller = 'tag';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Tag'));
        $this->_updateButton('delete', 'label', __('Delete Tag'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('tag_tag')->getId()) {
            return __('Edit Tag') . " '" . Mage::registry('tag_tag')->getName() . "'";
        }
        else {
            return __('New Tag');
        }
    }

}