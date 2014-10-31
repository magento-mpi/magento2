<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rule\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ConditionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rule\Model\ConditionFactory
     */
    protected $conditionFactory;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->conditionFactory = $this->objectManagerHelper->getObject(
            'Magento\Rule\Model\ConditionFactory',
            [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testConditionFactoryClassHasStaticArray()
    {
        $this->assertClassHasStaticAttribute('conditionModels', 'Magento\Rule\Model\ConditionFactory');
    }

    public function testExceptingToCallMethodCreateInObjectManager()
    {
        $type = 'type';
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($type)
            ->willReturn(new \stdClass());

        $this->conditionFactory->create($type);
    }

    public function testExceptingToCallMethodCreateInObjectManagerForSameTypeOnlyOnce()
    {
        $type = 'test';
        $this->objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($type)
            ->willReturn(new \stdClass());

        $this->conditionFactory->create($type);
        $this->conditionFactory->create($type);
    }

    public function testExceptingToCallMethodCreateInObjectManagerEachTimePerType()
    {
        $type2 = 'test2';
        $this->objectManagerMock
            ->expects($this->at(0))
            ->method('create')
            ->with($type2)
            ->willReturn(new \stdClass());

        $type3 = 'test3';
        $this->objectManagerMock
            ->expects($this->at(1))
            ->method('create')
            ->with($type3)
            ->willReturn(new \stdClass());

        $type4 = 'test4';
        $this->objectManagerMock
            ->expects($this->at(2))
            ->method('create')
            ->with($type4)
            ->willReturn(new \stdClass());

        $this->conditionFactory->create($type2);
        $this->conditionFactory->create($type3);
        $this->conditionFactory->create($type4);
    }
}
