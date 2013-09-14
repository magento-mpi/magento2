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

class Magento_AdminNotification_Model_System_Message_Media_Synchronization_SuccessTest
    extends PHPUnit_Framework_TestCase
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
     * @var \Magento\AdminNotification\Model\System\Message\Media\Synchronization\Success
     */
    protected $_model;

    public function setUp()
    {
        $this->_syncFlagMock = $this->getMock(
            'Magento\Core\Model\File\Storage\Flag', array('getState', 'getFlagData', 'setState'), array(), '', false
        );

        $this->_fileStorage = $this->getMock('Magento\Core\Model\File\Storage', array(), array(), '', false);
        $this->_fileStorage->expects($this->any())->method('getSyncFlag')
            ->will($this->returnValue($this->_syncFlagMock));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'fileStorage' => $this->_fileStorage,
        );
        $this->_model = $objectManagerHelper
            ->getObject('Magento\AdminNotification\Model\System\Message\Media\Synchronization\Success', $arguments);

    }

    public function testGetText()
    {
        $messageText = 'Synchronization of media storages has been completed';

        $this->assertContains($messageText, (string)$this->_model->getText());
    }


    /**
     * @param bool $expectedFirstRun
     * @param array $data
     * @param int|bool $state
     * @return void
     * @dataProvider isDisplayedDataProvider
     */
    public function testIsDisplayed($expectedFirstRun, $data, $state)
    {
        $arguments = array(
            'fileStorage' => $this->_fileStorage,
        );
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_syncFlagMock->expects($this->any())->method('getState')->will($this->returnValue($state));
        $this->_syncFlagMock->expects($this->any())->method('getFlagData')->will($this->returnValue($data));

        // create new instance to ensure that it hasn't been displayed yet (var $this->_isDisplayed is unset)
        /** @var $model \Magento\AdminNotification\Model\System\Message\Media\Synchronization\Success */
        $model = $objectManagerHelper
            ->getObject('Magento\AdminNotification\Model\System\Message\Media\Synchronization\Success', $arguments);
        //check first call
        $this->assertEquals($expectedFirstRun, $model->isDisplayed());
        //check second call
        $this->assertEquals($expectedFirstRun, $model->isDisplayed());
    }

    public function isDisplayedDataProvider()
    {
        return array(
            array(false, array('has_errors' => 1), \Magento\Core\Model\File\Storage\Flag::STATE_FINISHED),
            array(false, array('has_errors' => true), false),
            array(true, array(), \Magento\Core\Model\File\Storage\Flag::STATE_FINISHED),
            array(false, array('has_errors' => 0), \Magento\Core\Model\File\Storage\Flag::STATE_RUNNING),
            array(true, array('has_errors' => 0), \Magento\Core\Model\File\Storage\Flag::STATE_FINISHED)
        );
    }

    public function testGetIdentity()
    {
        $this->assertEquals('MEDIA_SYNCHRONIZATION_SUCCESS', $this->_model->getIdentity());
    }

    public function testGetSeverity()
    {
        $severity = \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_MAJOR;
        $this->assertEquals($severity, $this->_model->getSeverity());
    }

}
