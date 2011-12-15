<?php

abstract class Mage_Api2_Model_Request_Interpreter
{
    /**
     * Mage_Api2_Model_Renderer_Interface
     *
     * @static
     * @throws Exception
     * @param string $type
     * @return Mage_Api2_Model_Request_Interpreter_Interface
     */
    public static function factory($type = 'json')
    {
        $types = Mage_Api2_Helper_Data::getTypeMapping();

        if (!isset($types[$type])) {
            throw new Exception(sprintf('Invalid response media type "%s"', $type));
        }

        $class = sprintf('Mage_Api2_Model_Request_Interpreter_%s', ucfirst($types[$type]));
        $renderer = new $class;

        return $renderer;
    }
}
