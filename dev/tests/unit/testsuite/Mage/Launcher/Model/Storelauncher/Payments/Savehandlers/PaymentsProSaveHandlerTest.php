<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandlerTest
    extends Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_TestCaseAbstract
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);
        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler(
            $config,
            $backendConfigModel
        );
    }

    protected function tearDown()
    {
        $this->_saveHandler = null;
    }

    public function testSave()
    {
        $data = array();
        $data['groups']['account']['fields']['business_account']['value'] = 'test@example.com';

        $preparedData = array();
        $preparedData['account']['fields']['business_account']['value'] = 'test@example.com';
        $preparedData['global']['fields']['wpp']['value'] = 1;

        // Mock backend config model
        $backendConfigModel = $this->_getBackendConfigModelMock();
        $backendConfigModel->expects($this->once())
            ->method('setSection')
            ->with('paypal')
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->once())
            ->method('setGroups')
            ->with($preparedData)
            ->will($this->returnValue($backendConfigModel));

        $backendConfigModel->expects($this->once())
            ->method('save');
        // Mock core configuration model
        $config = $this->getMock(
            'Mage_Core_Model_Config',
            array('reinit'),
            array(),
            '',
            false
        );

        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue($config));

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler(
            $config,
            $backendConfigModel
        );
        $saveHandler->save($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Email address must have correct format.
     */
    public function testSaveThrowsExceptionWhenEmailIsNotValid()
    {
        $data = array();
        $data['groups']['account']['fields']['business_account']['value'] = 'invalid_email';
        $this->_saveHandler->save($data);
    }

    public function testPrepareData()
    {
        $data = array();
        $data['groups']['account']['fields']['business_account']['value'] = 'test@example.com';

        $preparedData = array();
        $preparedData['account']['fields']['business_account']['value'] = 'test@example.com';
        $preparedData['global']['fields']['wpp']['value'] = 1;
        $this->assertEquals($preparedData, $this->_saveHandler->prepareData($data));
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Email address must have correct format.
     */
    public function testPrepareDataThrowsExceptionWhenEmailIsNotValid()
    {
        $data = array();
        $data['groups']['account']['fields']['business_account']['value'] = 'invalid_email';
        $this->_saveHandler->prepareData($data);
    }
}
