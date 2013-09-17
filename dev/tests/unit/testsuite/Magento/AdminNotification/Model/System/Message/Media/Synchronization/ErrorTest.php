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

class Magento_AdminNotification_Model_System_Message_Media_Synchronization_ErrorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_syncFlagMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileStorage;

    /**
     * @var Magento_AdminNotification_Model_System_Message_Media_Synchronization_Error
     */
    protected $_model;

    protected function setUp()
    {
        $this->_syncFlagMock = $this->getMock('Magento_Core_Model_File_Storage_Flag', array(), array(), '', false);

        $this->_fileStorage = $this->getMock('Magento_Core_Model_File_Storage', array(), array(), '', false);
        $this->_fileStorage->expects($this->any())->method('getSyncFlag')
            ->will($this->returnValue($this->_syncFlagMock));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'fileStorage' => $this->_fileStorage,
        );
        $this->_model = $objectManagerHelper
            ->getObject('Magento_AdminNotification_Model_System_Message_Media_Synchronization_Error', $arguments);

    }

    public function testGetText()
    {
        $messageText = 'One or more media files failed to be synchronized';

        $this->assertContains($messageText, (string)$this->_model->getText());
    }

    /**
     * @param bool $expectedFirstRun
     * @param array $data
     * @dataProvider isDisplayedDataProvider
     */
    public function testIsDisplayed($expectedFirstRun, $data)
    {
        $arguments = array(
            'fileStorage' => $this->_fileStorage,
        );
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        // create new instance to ensure that it hasn't been displayed yet (var $this->_isDisplayed is unset)
        /** @var $model Magento_AdminNotification_Model_System_Message_Media_Synchronization_Error */
        $model = $objectManagerHelper
            ->getObject('Magento_AdminNotification_Model_System_Message_Media_Synchronization_Error', $arguments);

        $this->_syncFlagMock->expects($this->any())->method('setState');
        $this->_syncFlagMock->expects($this->any())->method('save');
        $this->_syncFlagMock->expects($this->any())->method('getFlagData')->will($this->returnValue($data));
        //check first call
        $this->assertEquals($expectedFirstRun, $model->isDisplayed());
        //check second call(another branch of if operator)
        $this->assertEquals($expectedFirstRun, $model->isDisplayed());
    }


    public function isDisplayedDataProvider()
    {
        return array(
            array(true, array('has_errors' => 1)),
            array(true, array('has_errors' => true)),
            array(false, array()),
            array(false, array('has_errors' => 0))
        );
    }

    public function testGetIdentity()
    {
        $this->assertEquals('MEDIA_SYNCHRONIZATION_ERROR', $this->_model->getIdentity());
    }

    public function testGetSeverity()
    {
        $severity = Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_MAJOR;
        $this->assertEquals($severity, $this->_model->getSeverity());
    }
}
