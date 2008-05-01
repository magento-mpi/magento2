<?php

class Mage_Paybox_Block_Adminhtml_Carte_Type_Select extends Mage_Adminhtml_Block_Template
{
    /**
     * Enter description here...
     *
     * @return Mage_Paybox_Model_System
     */
    public function getModel()
    {
        return Mage::getModel('paybox/system');
    }

    public function getParentHtmlId()
    {
        return substr($this->getDependHtmlId(), 0, strrpos($this->getDependHtmlId(), 'typecarte')) . 'typepaiement';
    }

    public function getJsonCarteTypes()
    {
        return $this->getModel()->getJsonCarteTypes();
    }

}