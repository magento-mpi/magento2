<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Mage_SomeModule_Helper_Test
{
    /**
     * @var Mage_SomeModule_ElementFactory_Proxy
     */
    protected $_factory;

    /**
     * @var Mage_SomeModule_Element_Proxy_Factory
     */
    protected $_proxy;

    public function __construct(Mage_SomeModule_ElementFactory $factory, Mage_SomeModule_Element_Proxy $proxy)
    {
        $this->_factory = $factory;
        $this->_proxy = $proxy;
    }

    /**
     * @return Mage_SomeModule_Block_Proxy
     */
    public function test(ModelFactory $factory)
    {
        return Mage::getModel('Mage_SomeModule_BlockFactory', array('data' => $factory));
    }
}