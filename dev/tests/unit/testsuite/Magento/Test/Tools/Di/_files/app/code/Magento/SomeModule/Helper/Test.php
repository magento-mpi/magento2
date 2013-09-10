<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class Magento_SomeModule_Helper_Test
{
    /**
     * @var Magento_SomeModule_ElementFactory_Proxy
     */
    protected $_factory;

    /**
     * @var Magento_SomeModule_Element_Proxy_Factory
     */
    protected $_proxy;

    public function __construct(Magento_SomeModule_ElementFactory $factory, Magento_SomeModule_Element_Proxy $proxy)
    {
        $this->_factory = $factory;
        $this->_proxy = $proxy;
    }

    /**
     * @return Magento_SomeModule_Block_Proxy
     */
    public function test(ModelFactory $factory)
    {
        return Mage::getModel('Magento_SomeModule_BlockFactory', array('data' => $factory));
    }
}
