<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Controller_Router_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Controller_Router_Default
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routeConfigMock;

    protected function setUp()
    {
        parent::setUp();

        $this->_routeConfigMock = $this->getMock('Mage_Core_Model_Route_Config', array(), array(), '', false);
        $options = array(
            'areaCode'        => Mage_Core_Model_App_Area::AREA_ADMINHTML,
            'baseController'  => 'Mage_Backend_Controller_ActionAbstract',
            'routeConfig' => $this->_routeConfigMock
        );
        $this->_frontMock = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
        $this->_model = Mage::getModel('Mage_Backend_Controller_Router_Default', $options);
        $this->_model->setFront($this->_frontMock);
    }

    public function testRouterCannotProcessRequestsWithWrongFrontName()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('frontend/admin/dashboard'));
        $this->_frontMock->expects($this->never())
            ->method('setDefault');
        $this->_model->match($request);
    }

    public function testRouterCanProcessRequestsWithProperFrontName()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('backend/admin/dashboard'));
        $this->_frontMock->expects($this->once())
            ->method('setDefault');

        $adminRoute = array(
            'adminhtml' => array(
                'id'        => 'adminhtml',
                'frontName' => 'admin',
                'modules'   => array(
                    'Mage_Adminhtml'
                )
            )
        );

        $this->_routeConfigMock->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue($adminRoute));
        $this->_model->match($request);
    }


    /**
     * @covers Mage_Backend_Controller_Router_Default::fetchDefault
     * @covers Mage_Backend_Controller_Router_Default::getDefaultModuleFrontName
     */
    public function testFetchDefault()
    {
        $default = array(
            'area' => '',
            'module' => 'admin',
            'controller' => 'index',
            'action' => 'index'
        );
        $routes = array(
            'adminhtml' => array(
                'id' => 'adminhtml',
                'frontName' => 'admin',
                'modules' => array()
            ),
            'key1' => array('frontName' => 'something'),
        );

        $this->_routeConfigMock->expects($this->once())->method('getRoutes')
            ->will($this->returnValue($routes));

        $this->_frontMock->expects($this->once())
            ->method('setDefault')
            ->with($this->equalTo($default));
        $this->_model->fetchDefault();
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @covers Mage_Backend_Controller_Router_Default::getControllerClassName
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->_model->getControllerClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Mage_Adminhtml', 'index', 'Mage_Adminhtml_Controller_Index'),
            array('Mage_Index', 'process', 'Mage_Index_Controller_Adminhtml_Process'),
            array('Mage_Index_Adminhtml', 'process', 'Mage_Index_Controller_Adminhtml_Process'),
        );
    }
}
