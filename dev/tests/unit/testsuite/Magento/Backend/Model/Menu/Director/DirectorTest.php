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

/**
 * Test class for Magento_Backend_Model_Menu_Director_Director
 */
class Magento_Backend_Model_Menu_Director_DirectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Magento_Backend_Model_Menu_Director_Director
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_commandFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_builderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_logger;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_commandMock;


    protected function setUp()
    {
        $this->_builderMock =
            $this->getMock('Magento_Backend_Model_Menu_Builder', array(), array(), '', false);
        $this->_logger = $this->getMock(
            'Magento_Core_Model_Logger', array('addStoreLog', 'log', 'logException'), array(), '', false
        );
        $this->_commandMock =
            $this->getMock('Magento_Backend_Model_Menu_Builder_CommandAbstract',
                array('getId', '_execute', 'execute', 'chain'), array(), '', false);
        $this->_commandFactoryMock =
            $this->getMock('Magento_Backend_Model_Menu_Builder_CommandFactory', array('create'), array(), '', false);
        $this->_commandFactoryMock
            ->expects($this->any())->method('create')->will($this->returnValue($this->_commandMock));

        $this->_commandMock->expects($this->any())->method('getId')->will($this->returnValue(true));
        $this->_model = new Magento_Backend_Model_Menu_Director_Director(
            $this->_commandFactoryMock
        );
    }

    public function testDirectWithExistKey()
    {
        $config = array(
            array('type' => 'update'),
            array('type' => 'remove' ),
            array('type' => 'added')
        );
        $this->_builderMock->expects($this->at(2))->method('processCommand')->with($this->_commandMock);
        $this->_logger->expects($this->at(1))->method('logDebug');
        $this->_commandMock->expects($this->at(1))->method('getId');
        $this->_model->direct($config, $this->_builderMock, $this->_logger);
    }

}
