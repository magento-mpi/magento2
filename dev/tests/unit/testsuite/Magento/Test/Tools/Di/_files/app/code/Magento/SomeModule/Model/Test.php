<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Magento_SomeModule_Model_Test
{
    public function __construct()
    {
        new Magento_SomeModule_Model_Element_Proxy();
    }

    /**
     * @param Magento_SomeModule_ModelFactory $factory
     * @param array $data
     */
    public function test(Magento_SomeModule_ModelFactory $factory, array $data = array())
    {
        $factory->create('Magento_SomeModule_Model_BlockFactory', array('data' => $data));
    }
}
