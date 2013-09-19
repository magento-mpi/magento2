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
        //Mage::getModel('Magento_SomeModule_Model_Comment_Element_Proxy', array('factory' => $factory));
    }

    /**
     * @param Magento_SomeModule_ModelFactory $factory
     * @param array $data
     */
    public function test(Magento_SomeModule_ModelFactory $factory, array $data = array())
    {
        /**
         * \Mage::getModel('Magento_SomeModule_Model_Comment_BlockFactory', array('factory' => $factory));
         */
        \Mage::getModel('Magento_SomeModule_Model_BlockFactory', array('factory' => $factory, 'data' => $data));
    }
}
