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
     * @var \Magento\SomeModule\ElementFactory\Proxy
     */
    protected $_factory;

    /**
     * @var \Magento\SomeModule\Element\Proxy\Factory
     */
    protected $_proxy;

    public function __construct(\Magento\SomeModule\ElementFactory $factory, \Magento\SomeModule\Element\Proxy $proxy)
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
        $factory->create('Magento\SomeModule\BlockFactory', array('data' => $data));
    }
}
