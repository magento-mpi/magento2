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
        $applicationMock = $this->getMock('\Magento\LauncherInterface');
        $applicationMock->expects($this->once())->method('launch')->will($this->returnValue(0));
        $this->_objectManagerMock->expects($this->once())->method('create')->with($applicationName, array())
            ->will($this->returnValue($applicationMock));

        $this->assertEquals(0, $this->_model->run($applicationName));
    }

    public function testRunCatchesExceptionThrownByApplication()
    {
        $applicationName = '\Magento\App\TestApplication';
        $applicationMock = $this->getMock('\Magento\LauncherInterface');
        $applicationMock->expects($this->once())
            ->method('launch')
            ->will($this->throwException(new \Exception('Something went wrong.')));
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with($applicationName, array())
            ->will($this->returnValue($applicationMock));
        // clean output
        ob_start();
        $this->assertEquals(1, $this->_model->run($applicationName));
        ob_end_clean();
    }
}
