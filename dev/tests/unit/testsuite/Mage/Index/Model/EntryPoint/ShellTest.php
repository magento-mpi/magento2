<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Index_Model_EntryPoint_IndexerMage_Index_Model_EntryPoint_Indexer
 */
class Mage_Index_Model_EntryPoint_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Index_Model_EntryPoint_Shelll
     */
    private $_entryPoint;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    private $_objectManager;

    /**
     * @var Mage_Index_Model_EntryPoint_Shell_ErrorHandler|PHPUnit_Framework_MockObject_MockObject
     */
    private $_shellErrorHandler;

    /**
     * @var Mage_Core_Model_Config_Primary|PHPUnit_Framework_MockObject_MockObject
     */
    private $_primaryConfig;

    protected function setUp()
    {
        $this->_primaryConfig = $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $this->_shellErrorHandler = $this->getMock(
            'Mage_Index_Model_EntryPoint_Shell_ErrorHandler',
            array(),
            array(),
            '',
            false
        );
        $this->_entryPoint = $this->getMock(
            'Mage_Index_Model_EntryPoint_Shell',
            array('_setGlobalObjectManager'),
            array('indexer.php', $this->_primaryConfig, $this->_objectManager, $this->_shellErrorHandler)
        );
    }

    /**
     * @param boolean $shellHasErrors
     * @dataProvider processRequestDataProvider
     */
    public function testProcessRequest($shellHasErrors)
    {
        $dirVerification = $this->getMock('Mage_Core_Model_Dir_Verification', array(), array(), '', false);
        $dirVerification->expects($this->once())->method('createAndVerifyDirectories')->will($this->returnValue(null));
        $shell = $this->getMock('Mage_Index_Model_Shell', array(), array(), '', false);
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

        // configure object manager
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(
                array(
                    array('Mage_Core_Model_Dir_Verification', $dirVerification),
                )
            ));
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap(
                array(
                    array('Mage_Index_Model_Shell', array(), $shell),
                )
            ));
        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(
                array(
                    'Mage_Index_Model_Shell' => array(
                        'parameters' => array(
                            'entryPoint' => 'indexer.php',
                        )
                    ),
                )
            );

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
