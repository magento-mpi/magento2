<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Message;

/**
 * \Magento\Framework\Message\Factory test case
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Message\Factory
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Framework\Message\Factory');
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate($messageType)
    {
        $message = $this->model->create($messageType, 'some text');
        $this->assertInstanceOf('\Magento\Framework\Message\MessageInterface', $message);
    }

    public function createProvider()
    {
        return array(
            array(MessageInterface::TYPE_SUCCESS),
            array(MessageInterface::TYPE_NOTICE),
            array(MessageInterface::TYPE_WARNING),
            array(MessageInterface::TYPE_ERROR)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong message type
     */
    public function testCreateWrong()
    {
        $this->model->create('Wrong', 'some text');
    }
}
