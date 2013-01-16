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

class Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandlerTest
    extends Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_TestCaseAbstract
{
    /**
     * @var Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler
     */
    protected $_saveHandler;

    protected function setUp()
    {
        // Mock backend config model
        $backendConfigModel = $this->getMock('Mage_Backend_Model_Config', array(), array(), '', false);
        // Mock core configuration model
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler(
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
        $data['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';
        $data['groups']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key ';

        $preparedData = array();
        $preparedData['authorizenet']['fields']['login']['value'] = 'API Login';
        $preparedData['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key';
        $preparedData['authorizenet']['fields']['active']['value'] = 1;

        // Mock backend config model
        $backendConfigModel = $this->_getBackendConfigModelMock();
        $backendConfigModel->expects($this->once())
            ->method('setSection')
            ->with('payment')
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

        $saveHandler = new Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler(
            $config,
            $backendConfigModel
        );
        $saveHandler->save($data);
    }

    public function testPrepareData()
    {
        $data = array();
        $data['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';
        $data['groups']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key ';

        $preparedData = array();
        $preparedData['authorizenet']['fields']['login']['value'] = 'API Login';
        $preparedData['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key';
        $preparedData['authorizenet']['fields']['active']['value'] = 1;

        $this->assertEquals($preparedData, $this->_saveHandler->prepareData($data));
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage API Login ID is required.
     */
    public function testPrepareDataThrowsExceptionWhenLoginIsNotSpecified()
    {
        $data = array();
        $data['groups']['authorizenet']['fields']['trans_key']['value'] = 'Transaction Key ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Transaction Key is required.
     */
    public function testPrepareDataThrowsExceptionWhenTransactionKeyIsNotSpecified()
    {
        $data = array();
        $data['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';
        $this->_saveHandler->prepareData($data);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Transaction Key is required.
     */
    public function testSaveDoesNotCatchExceptionThrownByPrepareData()
    {
        $data = array();
        $data['groups']['authorizenet']['fields']['login']['value'] = 'API Login ';
        $this->_saveHandler->save($data);
    }
}
