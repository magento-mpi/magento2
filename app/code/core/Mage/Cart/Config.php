<?php

class Mage_Cart_Config
{
    function getTotals($type='')
    {
        $config = Mage::getConfig()->getXml()->global->cartTotals;
        if (''!==$type) {
            return $config->$type;
        } else {
            return $config->children();
        }
    }
}