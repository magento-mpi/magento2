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

    /**
     * @var string
     */
    protected $_applicationName;

    protected function setUp()
    {

        $this->_applicationName = '\Magento\App\EntryPoint\DefaultApplication';

        $this->_parameters = array(
            'MAGE_MODE' => 'developer'
        );
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_rootDir = realpath(__DIR__ . '/../../../../../../../');

        $this->_model = new \Magento\App\EntryPoint\EntryPoint(
            $this->_rootDir,
            $this->_parameters,
            $this->_objectManagerMock
        );
    }

    public function testRun()
    {
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with($this->_applicationName, array())
            ->will($this->returnValue(new $this->_applicationName()));

        $this->assertEquals('expectedValue', $this->_model->run($this->_applicationName));
    }

    public function testRunWhenCatchException()
    {
        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('\Magento\App\EntryPoint\ApplicationWithException', array())
            ->will($this->returnValue(new \Magento\App\EntryPoint\ApplicationWithException()));
        // clean output
        ob_start();
        $this->assertEquals(1, $this->_model->run('\Magento\App\EntryPoint\ApplicationWithException'));
        ob_end_clean();
    }
}
