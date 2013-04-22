<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modelStoreMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var Saas_Saas_Helper_Data
     */
    protected $_saasHelper;

    protected function setUp()
    {
        $this->_modelStoreMock = $this->getMock('Mage_Core_Model_Store', array(), array(), '', false);
        $this->_storeManageMock = $this->getMock('Mage_Core_Model_StoreManagerInterface');
        $this->_storeManageMock->expects($this->atLeastOnce())->method('getStore')
            ->will($this->returnValue($this->_modelStoreMock));
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_saasHelper = $objectManagerHelper->getObject('Saas_Saas_Helper_Data', array(
            'storeManage' => $this->_storeManageMock,
            'config' => $this->_configMock,
        ));
    }

    public function testCustomizeNoRoutForward()
    {
        $this->_modelStoreMock->expects($this->once())->method('getConfig')->with('web/default/no_route')
            ->will($this->returnValue('module_no_route/controller_no_route/action_no_route'));
        $this->_modelStoreMock->expects($this->once())->method('isAdmin')
            ->will($this->returnValue(false));

        $this->_requestMock->expects($this->once())->method('initForward')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setModuleName')->with('module_no_route')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setControllerName')->with('controller_no_route')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setActionName')->with('action_no_route')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);

        $this->_saasHelper->customizeNoRoutForward($this->_requestMock);
    }

    public function testCustomizeNoRoutForwardWithEmptyConfigValue()
    {
        $this->_modelStoreMock->expects($this->once())->method('getConfig')->with('web/default/no_route')
            ->will($this->returnValue(''));
        $this->_modelStoreMock->expects($this->once())->method('isAdmin')
            ->will($this->returnValue(false));

        $this->_requestMock->expects($this->once())->method('initForward')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setModuleName')->with('core')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setControllerName')->with('index')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setActionName')->with('index')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);

        $this->_saasHelper->customizeNoRoutForward($this->_requestMock);
    }

    public function testStoreIfStoreIsAdminAndAdminFrontNameNotEqualModuleName()
    {
        $this->_modelStoreMock->expects($this->once())->method('getConfig')->with('web/default/no_route')
            ->will($this->returnValue('module_no_route/controller_no_route/action_no_route'));
        $this->_modelStoreMock->expects($this->once())->method('isAdmin')
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->once())->method('getNode')->with('admin/routers/adminhtml/args/frontName')
            ->will($this->returnValue('adminFrontName'));

        $this->_storeManageMock->expects($this->once())->method('getDefaultStoreView')
            ->will($this->returnValue('DefaultStoreView'));
        $this->_storeManageMock->expects($this->once())->method('setCurrentStore')->with('DefaultStoreView');

        $this->_requestMock->expects($this->once())->method('initForward')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setModuleName')->with('core')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setControllerName')->with('index')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setActionName')->with('noRoute')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);

        $this->_saasHelper->customizeNoRoutForward($this->_requestMock);
    }

    public function testStoreIfStoreIsAdminAndAdminFrontNameIsEqualModuleName()
    {
        $this->_modelStoreMock->expects($this->once())->method('getConfig')->with('web/default/no_route')
            ->will($this->returnValue('adminFrontName/controller_no_route/action_no_route'));
        $this->_modelStoreMock->expects($this->once())->method('isAdmin')
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->once())->method('getNode')->with('admin/routers/adminhtml/args/frontName')
            ->will($this->returnValue('adminFrontName'));

        $this->_requestMock->expects($this->once())->method('initForward')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setModuleName')->with('adminFrontName')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setControllerName')->with('controller_no_route')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setActionName')->with('action_no_route')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);

        $this->_saasHelper->customizeNoRoutForward($this->_requestMock);
    }
}
