<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\EntryPoint;

class EntryPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\EntryPoint\EntryPoint
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
        $this->_parameters = array('MAGE_MODE' => 'developer');
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->_responseMock = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
        $this->_rootDir = realpath(__DIR__ . '/../../../../../../../');
        $this->_model = new \Magento\Framework\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            $this->_parameters,
            $this->_objectManagerMock
        );
    }

    public function testRunExecutesApplication()
    {
        $applicationName = '\Magento\Framework\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\Framework\AppInterface');
        $applicationMock->expects($this->once())->method('launch')->will($this->returnValue($this->_responseMock));
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $applicationName,
            array()
        )->will(
            $this->returnValue($applicationMock)
        );
        $this->assertNull($this->_model->run($applicationName));
    }

    public function testRunCatchesExceptionThrownByApplication()
    {
        $applicationName = '\Magento\Framework\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\Framework\AppInterface');
        $applicationMock->expects(
            $this->once()
        )->method(
            'launch'
        )->will(
            $this->throwException(new \Exception('Something went wrong.'))
        );
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $applicationName,
            array()
        )->will(
            $this->returnValue($applicationMock)
        );
        // clean output
        ob_start();
        $this->assertNull($this->_model->run($applicationName));
        ob_end_clean();
    }
}
