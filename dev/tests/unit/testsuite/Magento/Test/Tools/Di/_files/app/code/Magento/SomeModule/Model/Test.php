<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\SomeModule\Model;

class Test
{
    public function __construct()
    {
        new Magento_SomeModule_Model_Element_Proxy();
    }

    /**
     * @param Magento_SomeModule_ModelFactory $factory
     * @param array $data
     */
    public function testModel(Magento_SomeModule_ModelFactory $factory, array $data = array())
    {
        $factory->create('Magento_SomeModule_Model_BlockFactory', array('data' => $data));
    }
}
