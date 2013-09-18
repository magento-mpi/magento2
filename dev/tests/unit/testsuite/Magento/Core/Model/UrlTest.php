<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Url
 */
class Magento_Core_Model_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Url
     */
    protected  $_model;

    protected function setUp()
    {
        $coreStoreConfig = $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false);
        $coreConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        
        $this->_model = new Magento_Core_Model_Url(
            $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false, false),
            $coreStoreConfig,
            $coreConfig
        );
    }

    public function testSetRoutePath()
    {
        $moduleFrontName = 'moduleFrontName';
        $controllerName = 'controllerName';
        $actionName = 'actionName';

        $this->assertNull($this->_model->getRouteName());
        $this->assertNull($this->_model->getControllerName());
        $this->assertNull($this->_model->getActionName());

        $this->_model->setRoutePath($moduleFrontName . '/' . $controllerName . '/' . $actionName);

        $this->assertNotNull($this->_model->getRouteName());
        $this->assertEquals($moduleFrontName, $this->_model->getRouteName());

        $this->assertNotNull($this->_model->getControllerName());
        $this->assertEquals($controllerName, $this->_model->getControllerName());

        $this->assertNotNull($this->_model->getActionName());
        $this->assertEquals($actionName, $this->_model->getActionName());
    }

    public function testSetRoutePathWhenAsteriskUses()
    {
        $moduleFrontName = 'moduleFrontName';
        $controllerName = 'controllerName';
        $actionName = 'actionName';

        $backendData = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $requestMock = $this->getMockForAbstractClass('Magento\Core\Controller\Request\Http',
            array($backendData), '', false, false, true,
            array('getRequestedRouteName', 'getRequestedControllerName', 'getRequestedActionName'));

        $requestMock->expects($this->once())->method('getRequestedRouteName')
            ->will($this->returnValue($moduleFrontName));
        $requestMock->expects($this->once())->method('getRequestedControllerName')
            ->will($this->returnValue($controllerName));
        $requestMock->expects($this->once())->method('getRequestedActionName')
            ->will($this->returnValue($actionName));

        $this->_model->setRequest($requestMock);

        $this->_model->setRoutePath('*/*/*');

        $this->assertEquals($moduleFrontName, $this->_model->getRouteName());
        $this->assertEquals($controllerName, $this->_model->getControllerName());
        $this->assertEquals($actionName, $this->_model->getActionName());
    }

    public function testSetRoutePathWhenRouteParamsExists()
    {
        $this->assertNull($this->_model->getControllerName());
        $this->assertNull($this->_model->getActionName());

        $this->_model->setRoutePath('m/c/a/p1/v1/p2/v2');

        $this->assertNotNull($this->_model->getControllerName());
        $this->assertNotNull($this->_model->getActionName());
        $this->assertNotEmpty($this->_model->getRouteParams());

        $this->assertArrayHasKey('p1', $this->_model->getRouteParams());
        $this->assertArrayHasKey('p2', $this->_model->getRouteParams());

        $this->assertEquals('v1', $this->_model->getRouteParam('p1'));
        $this->assertEquals('v2', $this->_model->getRouteParam('p2'));
    }
}
