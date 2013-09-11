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
     * @var \Magento\Index\Model\EntryPoint\Shell
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
        $this->_primaryConfig = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_shellErrorHandler = $this->getMock(
            '\Magento\Index\Model\EntryPoint\Shell\ErrorHandler',
            array(),
            array(),
            '',
            false
        );
        $this->_entryPoint = new \Magento\Index\Model\EntryPoint\Shell(
            'indexer.php', $this->_shellErrorHandler, $this->_primaryConfig, $this->_objectManager
        );
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $shell = $this->getMock('Magento\Index\Model\Shell', array(), array(), '', false);
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
                    array('Magento\Index\Model\Shell', array('entryPoint' => 'indexer.php'), $shell),
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
