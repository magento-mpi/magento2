<?php

class Mage_Adminhtml_Block_Tree extends Mage_Core_Block_Template 
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tree.phtml');
    }
}