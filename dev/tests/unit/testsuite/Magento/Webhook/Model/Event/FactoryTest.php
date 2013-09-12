<?php
/**
 * \Magento\Webhook\Model\Event\Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Event_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Event\Factory */
    protected $_factory;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_arrayConverter;

    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_arrayConverter = $this->getMockBuilder('Magento\Convert\Object')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_factory = new \Magento\Webhook\Model\Event\Factory($this->_objectManager, $this->_arrayConverter);
    }

    public function testCreate()
    {
        $webhookEvent = $this->getMockBuilder('Magento\Webhook\Model\Event')
            ->disableOriginalConstructor()
            ->getMock();
        $topic = 'TEST_TOPIC';
        $data = 'TEST_DATA';
        $array = 'TEST_ARRAY';
        $this->_arrayConverter->expects($this->once())
            ->method('convertDataToArray')
            ->with($this->equalTo($data))
            ->will($this->returnValue($array));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Webhook\Model\Event'),
                $this->equalTo(
                    array(
                         'data' => array(
                             'topic'     => $topic,
                             'body_data' => serialize($array)
                         )
                    )
                )
            )
            ->will($this->returnValue($webhookEvent));
        $webhookEvent->expects($this->once())
            ->method('setDataChanges')
            ->with($this->equalTo(true))
            ->will($this->returnSelf());
        $this->assertSame($webhookEvent, $this->_factory->create($topic, $data));
    }

    public function testCreateEmpty()
    {
        $testValue = "test value";
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento\Webhook\Model\Event'))
            ->will($this->returnValue($testValue));
        $this->assertSame($testValue, $this->_factory->createEmpty());
    }
}
