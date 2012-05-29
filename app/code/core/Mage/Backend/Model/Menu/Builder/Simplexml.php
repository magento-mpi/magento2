<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Simplexml
{
    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {

    }

    /**
     * @return Varien_Simplexml_Config
     */
    public function getResult()
    {
        return new Varien_Simplexml_Config();
    }

    public function processCommand(Mage_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        return $this;
    }
}
