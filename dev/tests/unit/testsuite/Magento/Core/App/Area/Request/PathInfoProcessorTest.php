<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\Area\Request;
class PathInfoProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\Request\PathInfoProcessor
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var string
     */
    protected $_pathInfo = '/storeCode/node_one/';

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('\Magento\App\RequestInterface',
            array('isDirectAccessFrontendName', 'getModuleName',
                'setModuleName', 'getActionName', 'setActionName', 'getParam'
            )
        );
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_model = new \Magento\Core\App\Request\PathInfoProcessor($this->_storeManagerMock);
    }

    public function testProcessIfStoreExistsAndIsNotDirectAcccessToFrontName()
    {
        $store = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())->method('getStores')->with(true, true)
            ->will($this->returnValue(array('storeCode' => $store)));
        $store->expects($this->once())->method('isUseStoreInUrl')->will($this->returnValue(true));
        $this->_requestMock
            ->expects($this->once())->method('isDirectAccessFrontendName')
            ->with('storeCode')->will($this->returnValue(false));
        $this->_storeManagerMock->expects($this->once())->method('setCurrentStore')->with('storeCode');
        $this->assertEquals('/node_one/', $this->_model->process($this->_requestMock, $this->_pathInfo));
    }

    public function testProcessIfStoreExistsAndDirectAcccessToFrontName()
    {
        $store = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())->method('getStores')->with(true, true)
            ->will($this->returnValue(array('storeCode' => $store)));
        $store->expects($this->once())->method('isUseStoreInUrl')->will($this->returnValue(true));
        $this->_requestMock
            ->expects($this->once())->method('isDirectAccessFrontendName')
            ->with('storeCode')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('setActionName')->with('noRoute');
        $this->assertEquals($this->_pathInfo, $this->_model->process($this->_requestMock, $this->_pathInfo));
    }

    public function testProcessIfStoreIsEmpty()
    {
        $path = '/0/node_one/';
        $store = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())->method('getStores')->with(true, true)
            ->will($this->returnValue(array('0' => $store)));
        $store->expects($this->once())->method('isUseStoreInUrl')->will($this->returnValue(true));
        $this->_requestMock
            ->expects($this->once())->method('isDirectAccessFrontendName')
            ->with('0')->will($this->returnValue(true));
        $this->_requestMock->expects($this->never())->method('setActionName');
        $this->assertEquals($path, $this->_model->process($this->_requestMock, $path));
    }

    public function testProcessIfStoreCodeIsNotExist()
    {
        $store = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $this->_storeManagerMock
            ->expects($this->once())->method('getStores')->with(true, true)
            ->will($this->returnValue(array('0' => $store)));
        $store->expects($this->never())->method('isUseStoreInUrl');
        $this->_requestMock->expects($this->never())->method('isDirectAccessFrontendName');
        $this->assertEquals($this->_pathInfo, $this->_model->process($this->_requestMock, $this->_pathInfo));
    }
}