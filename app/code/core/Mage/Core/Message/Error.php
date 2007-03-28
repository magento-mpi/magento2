<?php

class Mage_Core_Message_Error extends Mage_Core_Message_Abstract
{
    function getType()
    {
        return 'error';
    }
}