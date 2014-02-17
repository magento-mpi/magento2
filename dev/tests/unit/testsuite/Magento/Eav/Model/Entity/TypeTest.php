<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrSetFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $universalFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Core\Model\Context', array(), array(), '', false);
        $this->registryMock = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->attrFactoryMock = $this->getMock('Magento\Eav\Model\Entity\AttributeFactory',
            array(), array(), '', false);
        $this->attrSetFactoryMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\SetFactory',
            array(), array(), '', false);
        $this->storeFactoryMock = $this->getMock(
            'Magento\Eav\Model\Entity\StoreFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->universalFactoryMock = $this->getMock('Magento\Validator\UniversalFactory', array(), array(), '', false);
        $this->resourceMock = $this->getMockForAbstractClass(
            'Magento\Core\Model\Resource\Db\AbstractDb',
            array(),
            '',
            false,
            false,
            true,
            array('beginTransaction', 'rollBack', 'commit', 'getIdFieldName', '__wakeup')
        );

        $this->model = new Type(
            $this->contextMock,
            $this->registryMock,
            $this->attrFactoryMock,
            $this->attrSetFactoryMock,
            $this->storeFactoryMock,
            $this->universalFactoryMock,
            $this->resourceMock
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Store instance cannot be created.
     */
    public function testFetchNewIncrementIdRollsBackTransactionAndRethrowsExceptionIfProgramFlowIsInterrupted()
    {
        $this->model->setIncrementModel('\IncrementModel');
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        // Interrupt program flow by exception
        $exception = new \Exception('Store instance cannot be created.');
        $this->storeFactoryMock->expects($this->once())->method('create')->will($this->throwException($exception));
        $this->resourceMock->expects($this->once())->method('rollBack');
        $this->resourceMock->expects($this->never())->method('commit');

        $this->model->fetchNewIncrementId();
    }
}
