<?php

abstract class Mage_Api2_Model_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @static
     * @throws Mage_Api2_Exception
     * @param string $type
     * @return Mage_Api2_Model_Renderer_Interface
     */
    public static function factory($type)
    {
        $types = Mage_Api2_Helper_Data::getTypeMapping();

        if (!isset($types[$type])) {
            throw new Mage_Api2_Exception(sprintf('Invalid response media type "%s"', $type), 400);
        }

        /** @var $renderer Mage_Api2_Model_Renderer_Interface */
        $renderer = Mage::getModel('api2/renderer_'.$types[$type]);

        return $renderer;
    }
}
