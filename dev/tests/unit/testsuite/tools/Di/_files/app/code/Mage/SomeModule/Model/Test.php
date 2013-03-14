<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Mage_SomeModule_Model_Test
{
    public function __construct()
    {
        new Mage_SomeModule_Model_Element_Proxy();
        //Mage::getModel('Mage_SomeModule_Model_Comment_Element_Proxy', array('factory' => $factory));
    }

    /**
     * @param Mage_SomeModule_ModelFactory $factory
     * @param array $data
     */
    public function test(Mage_SomeModule_ModelFactory $factory, array $data = array())
    {
        /**
         * Mage::getModel('Mage_SomeModule_Model_Comment_BlockFactory', array('factory' => $factory));
         */
        Mage::getModel('Mage_SomeModule_Model_BlockFactory', array('factory' => $factory, 'data' => $data));
    }
}