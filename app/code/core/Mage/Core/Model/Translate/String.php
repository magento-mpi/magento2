<?php

class Mage_Core_Model_Translate_String extends Mage_Core_Model_Abstract
{
    function _construct()
    {
        $this->_init('core/translate_string');
    }

    function setString($string)
    {
        $this->setData('string', $string);
        return $this;
    }

    /**
     * Retrieve string
     *
     * @return string
     */
    function getString()
    {
        return $this->getData('string');
    }
}
