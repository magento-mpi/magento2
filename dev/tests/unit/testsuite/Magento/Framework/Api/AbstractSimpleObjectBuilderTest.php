<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Api;

class AbstractSimpleObjectBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StubAbstractSimpleObjectBuilder
     */
    protected $object;

    /**
     * @var \Magento\Framework\Api\ObjectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectFactory;

    protected function setUp()
    {
        $this->objectFactory = $this->getMock('\Magento\Framework\Api\ObjectFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->object = new StubAbstractSimpleObjectBuilder($this->objectFactory);
    }

    protected function tearDown()
    {
        $this->object = null;
        $this->objectFactory = null;
    }

    public function testCreate()
    {
        $testObject = new \stdClass();
        $this->objectFactory->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Framework\Api\StubAbstractSimpleObject'), $this->equalTo([
                'builder' => $this->object,
                'data' => $this->object->getData()
            ]))
            ->will($this->returnValue($testObject));

        $result = $this->object->create();
        $this->assertEquals($testObject, $result);
    }

    public function testMergeDataObjectWithArray()
    {
        $simpleObjectData = ['key1' => 'value1'];
        $data = ['key2' => 'value2'];
        $mergedObjectData = ['key1' => 'value1', 'key2' => 'value2'];

        $simpleObject = $this->getSimpleObjectMock($simpleObjectData, $data);
        $mergedObject = $this->getSimpleObjectMock($mergedObjectData);

        $this->objectFactory->expects($this->once())
            ->method('create')
            ->with(
                'Magento\Framework\Api\StubAbstractSimpleObject',
                [
                    'builder' => $this->object,
                    'data' => $this->object->getData()
                ]
            )
            ->will($this->returnValue($mergedObject));

        $this->assertEquals($mergedObject, $this->object->mergeDataObjectWithArray($simpleObject, $data));
    }

    private function getSimpleObjectMock($data)
    {
        $simpleObject = $this->getMockBuilder('Magento\Framework\Api\StubAbstractSimpleObject')
            ->setMethods(['__toArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $simpleObject->expects($this->any())
            ->method('__toArray')
            ->willReturn($data);
        return $simpleObject;
    }
}
