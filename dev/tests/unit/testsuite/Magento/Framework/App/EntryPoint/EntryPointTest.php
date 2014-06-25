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
     * @return void
     */
    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->_responseMock = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
        $this->_rootDir = realpath(__DIR__ . '/../../../../../../../');
    }

    /**
     * @return void
     */
    public function testRunExecutesApplication()
    {
        $this->_model = new \Magento\Framework\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            array(),
            $this->_objectManagerMock
        );

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

    /**
     * @return void
     */
    public function testRunCatchesExceptionThrownByApplicationDeveloperMode()
    {
        $this->_model = new \Magento\Framework\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            array('MAGE_MODE' => 'developer'),
            $this->_objectManagerMock
        );

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

    /**
     * @return void
     */
    public function testRunCatchesExceptionThrownByApplicationNonDeveloperMode()
    {
        $this->_model = new \Magento\Framework\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            array(),
            $this->_objectManagerMock
        );

        $exception = new \Exception('Something went wrong.');

        $applicationName = '\Magento\Framework\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\Framework\AppInterface');
        $applicationMock->expects(
            $this->once()
        )->method(
            'launch'
        )->will(
            $this->throwException($exception)
        );

        $loggerMock = $this->getMock(
            '\Magento\Framework\Logger',
            array(),
            array(),
            '',
            false
        );
        $loggerMock->expects(
            $this->once()
        )->method(
            'logException'
        )->will(
            $this->throwException($exception)
        );

        $this->_objectManagerMock->expects(
            $this->at(0)
        )->method(
            'create'
        )->with(
            $applicationName,
            array()
        )->will(
            $this->returnValue($applicationMock)
        );
        $this->_objectManagerMock->expects(
            $this->at(1)
        )->method(
            'get'
        )->with(
            'Magento\Framework\Logger'
        )->will(
            $this->returnValue($loggerMock)
        );

        // clean output
        ob_start();
        $this->assertNull($this->_model->run($applicationName));
        ob_end_clean();
    }
}
