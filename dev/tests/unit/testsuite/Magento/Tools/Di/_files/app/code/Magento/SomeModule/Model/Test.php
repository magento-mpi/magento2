<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\SomeModule\Model;

/**
 * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
 */
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
    public function testModel(\Magento\SomeModule\ModelFactory $factory, array $data = [])
    {
        $factory->create('Magento\SomeModule\Model\BlockFactory', ['data' => $data]);
    }
}
