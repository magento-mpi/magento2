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

class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandlerTest
    extends Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_TestCaseAbstract
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);
        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler(
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
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $data['groups']['payflow_advanced']['fields']['pwd']['value'] = 'password ';

        $preparedData = array();
        $preparedData['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner';
        $preparedData['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor';
        $preparedData['payflow_advanced']['fields']['user']['value'] = 'user';
        $preparedData['payflow_advanced']['fields']['pwd']['value'] = 'password';
        $preparedData['global']['fields']['payflow_advanced']['value'] = 1;

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

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler(
            $config,
            $backendConfigModel
        );
        $saveHandler->save($data);
    }

    public function testPrepareData()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $data['groups']['payflow_advanced']['fields']['pwd']['value'] = 'password ';

        $preparedData = array();
        $preparedData['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner';
        $preparedData['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor';
        $preparedData['payflow_advanced']['fields']['user']['value'] = 'user';
        $preparedData['payflow_advanced']['fields']['pwd']['value'] = 'password';
        $preparedData['global']['fields']['payflow_advanced']['value'] = 1;

        $this->assertEquals($preparedData, $this->_saveHandler->prepareData($data));
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Partner field is required.
     */
    public function testPrepareDataThrowsExceptionWhenPartnerIsNotSpecified()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $data['groups']['payflow_advanced']['fields']['pwd']['value'] = 'password ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Vendor field is required.
     */
    public function testPrepareDataThrowsExceptionWhenVendorIsNotSpecified()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $data['groups']['payflow_advanced']['fields']['pwd']['value'] = 'password ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage User field is required.
     */
    public function testPrepareDataThrowsExceptionWhenUserIsNotSpecified()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['pwd']['value'] = 'password ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Password field is required.
     */
    public function testPrepareDataThrowsExceptionWhenPasswordIsNotSpecified()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Password field is required.
     */
    public function testSaveDoesNotCatchExceptionThrownByPrepareData()
    {
        $data = array();
        $data['groups']['payflow_advanced']['fields']['partner']['value'] = 'PayPal Partner ';
        $data['groups']['payflow_advanced']['fields']['vendor']['value'] = 'PayPal Vendor ';
        $data['groups']['payflow_advanced']['fields']['user']['value'] = 'user ';
        $this->_saveHandler->save($data);
    }
}
