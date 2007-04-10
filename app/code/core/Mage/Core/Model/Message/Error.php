<?php

class Mage_Core_Model_Message_Error extends Mage_Core_Model_Message_Abstract
{
    function getType()
    {
        return 'error';
    }
}