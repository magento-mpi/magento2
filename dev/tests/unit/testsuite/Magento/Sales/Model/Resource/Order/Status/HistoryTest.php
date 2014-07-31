<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Status;

/**
 * Class HistoryTest
 * @package Magento\Sales\Model\Resource\Order\Status
 */
class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Resource\Order\Status\History
     */
    protected $historyResource;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResourceMock;

    /**
     * @var \Magento\Sales\Model\Order\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyMock;

    /**
     * @var \Magento\Framework\DB\Adapter\Pdo\Mysql|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Magento\Sales\Model\Order\Address\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;


    public function setUp()
    {
        $this->historyMock = $this->getMock(
            'Magento\Sales\Model\Order\Status\History',
            [],
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
            'Magento\Sales\Model\Resource\Order\Status\History\Validator',
            [],
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
        $this->historyResource = $objectManager->getObject(
            'Magento\Sales\Model\Resource\Order\Status\History',
            [
                'resource' => $this->appResourceMock,
                'validator' => $this->validatorMock
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
            ->will($this->returnValue([]));
        $this->historyResource->save($this->historyMock);
    }

    /**
     * test _beforeSaveMethod via save()
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Cannot save comment:
     */
    public function testValidate()
    {
        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(['Some warnings']));
        $this->historyResource->save($this->historyMock);
    }
}
