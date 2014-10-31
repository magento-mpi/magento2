<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rule\Model\Condition\Combine | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $combine;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Rule\Model\Condition\Context | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Rule\Model\ConditionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactoryMock;

    /**
     * @var \Magento\Framework\Logger | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * Sets up the Mocks.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->conditionFactoryMock = $this->getMockBuilder('\Magento\Rule\Model\ConditionFactory')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->loggerMock = $this->getMockBuilder('\Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->contextMock = $this->getMockBuilder('\Magento\Rule\Model\Condition\Context')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->contextMock
            ->expects($this->once())
            ->method('getConditionFactory')
            ->will($this->returnValue($this->conditionFactoryMock));

        $this->contextMock
            ->expects($this->once())
            ->method('getLogger')
            ->will($this->returnValue($this->loggerMock));

        $this->combine = (new ObjectManagerHelper($this))->getObject(
            '\Magento\Rule\Model\Condition\Combine',
            [
                "context"    => $this->contextMock,
            ]
        );
    }

    /**
     *
     * @covers \Magento\Rule\Model\Condition\AbstractCondition::getValueName
     *
     * @dataProvider optionValuesData
     *
     * @param string|array $value
     * @param string $expectingData
     */
    public function testGetValueName($value, $expectingData)
    {
        $this->combine
            ->setValueOption(['option_key' => 'option_value'])
            ->setValue($value);

        $this->assertEquals($expectingData, $this->combine->getValueName());
    }

    /**
     * @return array
     */
    public function optionValuesData()
    {
        return [
            ['option_key', 'option_value'],
            ['option_value', 'option_value'],
            [['option_key'], 'option_value'],
            ['', '...'],
        ];
    }

    public function testLoadArray()
    {
        $array['conditions'] = [
            [
                'type' => 'test',
                'attribute' => '',
                'operator' => '',
                'value' => '',
            ]
        ];

        $objectMock = $this->getMockBuilder('\Magento\SalesRule\Model\Rule\Condition\Product')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $objectMock->expects($this->once())
            ->method('loadArray')
            ->with($array['conditions'][0], 'conditions');

        $this->conditionFactoryMock->expects($this->once())
            ->method('create')
            ->with($array['conditions'][0]['type'])
            ->willReturn($objectMock);

        $this->loggerMock->expects($this->never())
            ->method('logException');

        $result = $this->combine->loadArray($array);

        $this->assertInstanceOf('\Magento\Rule\Model\Condition\Combine', $result);
    }

    public function testLoadArrayLoggerCatchException()
    {
        $array['conditions'] = [
            [
                'type' => '',
                'attribute' => '',
                'operator' => '',
                'value' => '',
            ]
        ];

        $objectMock = $this->getMockBuilder('\Magento\SalesRule\Model\Rule\Condition\Product')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $objectMock->expects($this->never())
            ->method('loadArray');

        $this->conditionFactoryMock->expects($this->once())
            ->method('create')
            ->with($array['conditions'][0]['type'])
            ->willThrowException(new \Exception('everything is fine, it is test'));

        $this->loggerMock->expects($this->once())
            ->method('logException')
            ->with();

        $result = $this->combine->loadArray($array);

        $this->assertInstanceOf('\Magento\Rule\Model\Condition\Combine', $result);
    }
}
