<?php

class Mage_Core_Block_Admin_Js extends Mage_Core_Block_Text_List
{
    function construct()
    {
        
    }
    
    function toJs()
    {
        
    }
    
    function toString()
    {
        return "Ext.Mage['".$this->getInfo('name')."']";
    }
}