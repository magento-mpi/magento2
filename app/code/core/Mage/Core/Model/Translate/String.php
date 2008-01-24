<?php

class Varien_Model_Translate_String extends Varien_Model_Abstract
{
    function _construct()
    {
        $this->_init('varien/translate_string');
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
