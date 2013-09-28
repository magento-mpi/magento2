<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\SomeModule\Helper;

class Test
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
     * @param ModelFactory $factory
     * @param array $data
     */
    public function test(ModelFactory $factory, array $data = array())
    {
        $factory->create('Magento_SomeModule_BlockFactory', array('data' => $data));
    }
}
