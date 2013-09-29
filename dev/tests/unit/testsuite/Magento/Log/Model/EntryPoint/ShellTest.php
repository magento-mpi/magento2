<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Log\Model\EntryPoint;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\Model\EntryPoint\Shell
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $config = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);
        $entryFileName = 'shell.php';
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Log\Model\EntryPoint\Shell($config, $entryFileName, $this->_objectManagerMock);
    }

    public function testProcessRequest()
    {
        $shellMock = $this->getMock('Magento\Log\Model\Shell', array(), array(), '', false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Log\Model\Shell', array('entryPoint' => 'shell.php'))
            ->will($this->returnValue($shellMock));

        $shellMock->expects($this->once())->method('run');
        $this->_model->processRequest();
    }
}
