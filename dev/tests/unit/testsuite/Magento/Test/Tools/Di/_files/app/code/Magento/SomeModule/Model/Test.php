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
        new \Magento\SomeModule\Model\Element\Proxy();
    }

    /**
     * @param \Magento\SomeModule\ModelFactory $factory
     * @param array $data
     */
    public function test(\Magento\SomeModule\ModelFactory $factory, array $data = array())
    {
        $factory->create('Magento\SomeModule\Model\BlockFactory', array('data' => $data));
    }
}
