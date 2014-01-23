<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\EntryPoint;

class EntryPointTest extends  \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\EntryPoint\EntryPoint
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var string
     */
    protected $_rootDir;

    /**
     * @var array()
     */
    protected $_parameters;

    protected function setUp()
    {
        $this->_parameters = array(
            'MAGE_MODE' => 'developer',
        );
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->_rootDir = realpath(__DIR__ . '/../../../../../../../');
        $this->_model = new \Magento\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            $this->_parameters,
            $this->_objectManagerMock
        );
    }

    public function testRunExecutesApplication()
    {
        $applicationName = '\Magento\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\AppInterface');
        $applicationMock->expects($this->once())->method('execute')->will($this->returnValue($this->_responseMock));
        $this->_objectManagerMock->expects($this->once())->method('create')->with($applicationName, array())
            ->will($this->returnValue($applicationMock));
        $this->assertNull($this->_model->run($applicationName));
    }

    public function testRunCatchesExceptionThrownByApplication()
    {
        $applicationName = '\Magento\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\AppInterface');
        $applicationMock->expects($this->once())
            ->method('execute')
            ->will($this->throwException(new \Exception('Something went wrong.')));
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($applicationName, array())
            ->will($this->returnValue($applicationMock));
        $this->assertNull($this->_model->run($applicationName));
    }
}
