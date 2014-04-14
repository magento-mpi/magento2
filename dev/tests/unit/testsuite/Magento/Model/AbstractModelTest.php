<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Model;

class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Model\AbstractModel
     */
    protected $model;

    /**
     * @var \Magento\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Model\Resource\Db\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\Db|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    /**
     * @var \Magento\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $actionValidatorMock;

    protected function setUp()
    {
        $this->actionValidatorMock = $this->getMock(
            '\Magento\Model\ActionValidator\RemoveAction', array(), array(), '', false
        );
        $this->contextMock = new \Magento\Model\Context(
            $this->getMock('Magento\Logger', array(), array(), '', false),
            $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Framework\App\CacheInterface', array(), array(), '', false),
            $this->getMock('Magento\Framework\App\State', array(), array(), '', false),
            $this->actionValidatorMock
        );
        $this->registryMock = $this->getMock('Magento\Registry', array(), array(), '', false);
        $this->resourceMock = $this->getMock(
            'Magento\Model\Resource\Db\AbstractDb',
            array(
                '_construct',
                '_getReadAdapter',
                '_getWriteAdapter',
                '__wakeup',
                'commit',
                'delete',
                'getIdFieldName',
                'rollBack'
            ),
            array(),
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock('Magento\Framework\Data\Collection\Db', array(), array(), '', false);
        $this->model = $this->getMockForAbstractClass(
            'Magento\Model\AbstractModel',
            array($this->contextMock, $this->registryMock, $this->resourceMock, $this->resourceCollectionMock)
        );
        $this->adapterMock = $this->getMock('Magento\DB\Adapter\AdapterInterface', array(), array(), '', false);
        $this->resourceMock->expects($this->any())
            ->method('_getWriteAdapter')
            ->will($this->returnValue($this->adapterMock));
        $this->resourceMock->expects($this->any())
            ->method('_getReadAdapter')
            ->will($this->returnValue($this->adapterMock));
    }

    public function testDelete()
    {
        $this->actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->adapterMock->expects($this->once())
            ->method('beginTransaction');
        $this->resourceMock->expects($this->once())
            ->method('delete');
        $this->resourceMock->expects($this->once())
            ->method('commit');
        $this->model->delete();
        $this->assertTrue($this->model->isDeleted());
    }

    /**
     * @expectedException \Exception
     */
    public function testDeleteRaiseException()
    {
        $this->actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $this->adapterMock->expects($this->once())
            ->method('beginTransaction');
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception));
        $this->resourceMock->expects($this->never())
            ->method('commit');
        $this->resourceMock->expects($this->once())
            ->method('rollBack');
        $this->model->delete();
    }

    /**
     * @expectedException \Magento\Model\Exception
     * @expectedExceptionMessage Delete operation is forbidden for current area
     */
    public function testDeleteOnModelThatCanNotBeRemoved()
    {
        $this->actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(false));
        $this->model->delete();
    }
}
