<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_EntryPoint_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_EntryPoint_Shell
     */
    protected $_entryPoint;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_shellErrorHandler;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_primaryConfig;

    protected function setUp()
    {
        $this->_primaryConfig = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_shellErrorHandler = $this->getMock(
            'Magento_Index_Model_EntryPoint_Shell_ErrorHandler',
            array(),
            array(),
            '',
            false
        );
        $this->_entryPoint = $this->getMock(
            'Magento_Index_Model_EntryPoint_Shell',
            array('_setGlobalObjectManager'),
            array('indexer.php', $this->_shellErrorHandler, $this->_primaryConfig, $this->_objectManager)
        );
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $shell = $this->getMock('Magento_Index_Model_Shell', array(), array(), '', false);
        $shell->expects($this->once())
            ->method('hasErrors')
            ->will($this->returnValue($shellHasErrors));
        $shell->expects($this->once())
            ->method('run');

        if ($shellHasErrors) {
            $this->_shellErrorHandler->expects($this->once())
                ->method('terminate')
                ->with(1);
        }
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(
                array(
                    array('Magento_Index_Model_Shell', array('entryPoint' => 'indexer.php'), $shell),
                )
            ));

        $this->_entryPoint->processRequest();
    }

    /**
     * @return array
     */
    public function processRequestDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }
}
