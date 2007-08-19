<?php

class Mage_Adminhtml_Block_Tax_Rate_ImportExport extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tax/importExport.phtml');
    }

}