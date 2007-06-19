<?php

class Mage_Adminhtml_Block_Widget_Tabsleft extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/widget/tabsleft.phtml');
    }
}