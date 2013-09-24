<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Menu\Builder;

class CommandAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Builder\CommandAbstract
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento\Backend\Model\Menu\Builder\CommandAbstract',
            array(array('id' => 'item'))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorRequiresObligatoryParams()
    {
        $this->getMockForAbstractClass('Magento\Backend\Model\Menu\Builder\CommandAbstract');
    }

    public function testChainAddsNewCommandAsNextInChain()
    {
        $command1 = $this->getMock('Magento\Backend\Model\Menu\Builder\Command\Update', array(),
            array(array('id' => 1)));
        $command2 = $this->getMock('Magento\Backend\Model\Menu\Builder\Command\Remove', array(),
            array(array('id' => 1)));
        $command1->expects($this->once())->method('chain')->with($this->equalTo($command2));

        $this->_model->chain($command1);
        $this->_model->chain($command2);
    }

    public function testExecuteCallsNextCommandInChain()
    {
        $itemParams = array();
        $this->_model->expects($this->once())
            ->method('_execute')
            ->with($this->equalTo($itemParams))
            ->will($this->returnValue($itemParams));

        $command1 = $this->getMock('Magento\Backend\Model\Menu\Builder\Command\Update', array(),
            array(array('id' => 1)));
        $command1->expects($this->once())
            ->method('execute')
            ->with($this->equalTo($itemParams))
            ->will($this->returnValue($itemParams));

        $this->_model->chain($command1);
        $this->assertEquals($itemParams, $this->_model->execute($itemParams));
    }
}
