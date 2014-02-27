<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\App\Router;

/**
 * @magentoAppArea adminhtml
 */
class DefaultRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Router\DefaultRouter
     */
    protected $_model;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento\Backend\App\Router\DefaultRouter');
    }

    public function testRouterCanProcessRequestsWithProperPathInfo()
    {
        $request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('backend/admin/dashboard'));

        $this->assertInstanceOf('Magento\Backend\Controller\Adminhtml\Dashboard', $this->_model->match($request));
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->_model->getControllerClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Magento_Index', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
            array('Magento_Index_Adminhtml', 'process', 'Magento\Index\Controller\Adminhtml\Process'),
        );
    }
}
