<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\App;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\App\Shell
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shellFactoryMock;

    protected function setUp()
    {
        $this->_shellFactoryMock = $this->getMock('Magento\Log\Model\ShellFactory',
            array('create'), array(), '', false);
        $this->_model = new \Magento\Log\App\Shell('shell.php', $this->_shellFactoryMock);
    }

    public function testProcessRequest()
    {
        $shellMock = $this->getMock('Magento\Log\App\Shell', array('run'), array(), '', false);
        $this->_shellFactoryMock->expects($this->once())
            ->method('create')
            ->with(array('entryPoint' => 'shell.php'))
            ->will($this->returnValue($shellMock));
        $shellMock->expects($this->once())->method('run');
        $this->assertEquals('0', $this->_model->launch());
    }
}
