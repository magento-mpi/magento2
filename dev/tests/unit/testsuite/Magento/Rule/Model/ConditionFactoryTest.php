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

    public function testExceptingClonedObject()
    {
        $origin = new \stdClass();

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('clone')
            ->willReturn($origin);

        $cloned = $this->conditionFactory->create('clone');

        $this->assertNotEquals(spl_object_hash($cloned), spl_object_hash($origin));
    }

    /**
     * @dataProvider dataProviderForExceptingToCallMethodCreateInObjectManagerForSameTypeOnlyOnce
     *
     * @param $type
     * @param $expects
     *
     */
    public function testExceptingToCallMethodCreateInObjectManagerForSameTypeOnlyOnce($type, $expects)
    {
        $this->objectManagerMock->expects($expects)
            ->method('create')
            ->with($type)
            ->willReturn(new \stdClass());

        $this->conditionFactory->create($type);
    }

    public function dataProviderForExceptingToCallMethodCreateInObjectManagerForSameTypeOnlyOnce()
    {
        return [
            1 => ['test', $this->once()],
            2 => ['test', $this->never()],
        ];
    }

    /**
     * @dataProvider dataProviderForExceptingToCallMethodCreateInObjectManagerEachTimePerType
     *
     * @param $type
     * @param $expects
     */
    public function testExceptingToCallMethodCreateInObjectManagerEachTimePerType($type, $expects)
    {
        $this->objectManagerMock->expects($expects)
            ->method('create')
            ->with($type)
            ->willReturn(new \stdClass());

        $this->conditionFactory->create($type);
    }

    public function dataProviderForExceptingToCallMethodCreateInObjectManagerEachTimePerType()
    {
        return [
            1 => ['test2', $this->once()],
            2 => ['test3', $this->once()],
            3 => ['test4', $this->once()],
            4 => ['test4', $this->never()],
        ];
    }
}
