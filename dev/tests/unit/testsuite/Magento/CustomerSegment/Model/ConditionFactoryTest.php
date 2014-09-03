<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Model;

class ConditionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConditionFactory
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Rule\Model\Condition\AbstractCondition
     */
    protected $abstractCondition;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    protected function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager');

        $this->context = $this->getMockBuilder('Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->abstractCondition = $this->getMockForAbstractClass(
            'Magento\Rule\Model\Condition\AbstractCondition', [$this->context]
        );

        $this->model = new ConditionFactory(
            $this->objectManager
        );
    }

    protected function tearDown()
    {
        unset(
            $this->model,
            $this->objectManager,
            $this->abstractCondition,
            $this->context
        );
    }

    public function testCreate()
    {
        $className = 'TestClass';
        $classNamePrefix = 'Magento\CustomerSegment\Model\Segment\Condition\\';

        $this->objectManager
            ->expects($this->once())
            ->method('create')
            ->with($classNamePrefix . $className)
            ->will($this->returnValue($this->abstractCondition));

        $result = $this->model->create($classNamePrefix . $className);

        $this->assertInstanceOf('Magento\Rule\Model\Condition\AbstractCondition', $result);
    }

    public function testCreateWithError()
    {
        $className = 'TestClass';
        $classNamePrefix = 'Magento\CustomerSegment\Model\Segment\Condition\\';

        $this->objectManager
            ->expects($this->once())
            ->method('create')
            ->with($classNamePrefix . $className)
            ->will($this->returnValue(new \StdClass()));

        $this->setExpectedException(
            'InvalidArgumentException',
            $classNamePrefix . $className . ' doesn\'t extends \Magento\Rule\Model\Condition\AbstractCondition'
        );

        $this->model->create($className);
    }
}
