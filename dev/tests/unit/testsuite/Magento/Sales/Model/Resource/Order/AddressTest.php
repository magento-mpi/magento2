<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

/**
 * Class AddressTest
 */
class AddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Address
     */
    protected $addressResource;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResourceMock;

    /**
     * @var \Magento\Sales\Model\Order\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $addressMock;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Magento\Sales\Model\Order\Address\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \Magento\Sales\Model\Resource\GridPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $gridPoolMock;

    public function setUp()
    {
        $this->addressMock = $this->getMock(
            'Magento\Sales\Model\Order\Address',
            ['__wakeup', 'getOrderId', 'hasDataChanges'],
            [],
            '',
            false
        );
        $this->appResourceMock = $this->getMock(
            'Magento\Framework\App\Resource',
            [],
            [],
            '',
            false
        );
        $this->adapterMock = $this->getMock(
            'Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['describeTable', 'insert', 'lastInsertId'],
            [],
            '',
            false
        );
        $this->validatorMock = $this->getMock(
            'Magento\Sales\Model\Order\Address\Validator',
            [],
            [],
            '',
            false
        );
        $this->gridPoolMock = $this->getMock(
            'Magento\Sales\Model\Resource\GridPool',
            ['refreshByOrderId'],
            [],
            '',
            false
        );
        $this->appResourceMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->adapterMock));
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->adapterMock->expects($this->any())
            ->method('describeTable')
            ->will($this->returnValue([]));
        $this->adapterMock->expects($this->any())
            ->method('insert');
        $this->adapterMock->expects($this->any())
            ->method('lastInsertId');
        $this->addressResource = $objectManager->getObject(
            'Magento\Sales\Model\Resource\Order\Address',
            [
                'resource' => $this->appResourceMock,
                'validator' => $this->validatorMock,
                'gridPool' => $this->gridPoolMock
            ]
        );
    }

    /**
     * test _beforeSaveMethod via save()
     */
    public function testSave()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($this->addressMock))
            ->will($this->returnValue([]));
        $this->addressMock->expects($this->once())
            ->method('hasDataChanges')
            ->will($this->returnValue(true));
        $this->addressMock->expects($this->exactly(2))
            ->method('getOrderId')
            ->will($this->returnValue(2));
        $this->gridPoolMock->expects($this->once())
            ->method('refreshByOrderId')
            ->with($this->equalTo(2))
            ->will($this->returnSelf());

        $this->addressResource->save($this->addressMock);
        $this->assertTrue(true);
    }

    /**
     * test _beforeSaveMethod via save() with failed validation
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Cannot save address:
     */
    public function testSaveValidationFailed()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($this->addressMock))
            ->will($this->returnValue(['warning message']));
        $this->addressResource->save($this->addressMock);
        $this->assertTrue(true);
    }
}
