<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule;

use Magento\TestFramework\Helper\ObjectManager;

class ConditionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reminder\Model\Rule\ConditionFactory
     */
    private $model;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $helper->getObject(
            'Magento\Reminder\Model\Rule\ConditionFactory',
            ['objectManager' => $this->objectManager]
        );
    }

    public function testCreate()
    {
        $type = 'Magento\Reminder\Model\Rule\Condition\Cart\Amount';

        $amount = $this->getMockBuilder($type)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())->method('create')->will($this->returnValue($amount));

        $result = $this->model->create($type);

        $this->assertInstanceOf("\\$type", $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Condition type is unexpected
     */
    public function testCreateInvalidArgumentException()
    {
        $type = 'someInvalidType';

        $this->model->create($type);
    }
}
