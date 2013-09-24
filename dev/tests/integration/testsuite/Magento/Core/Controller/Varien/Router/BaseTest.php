<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller\Varien\Router;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Controller\Varien\Router\Base
     */
    protected $_model;

    protected function setUp()
    {
        $options = array(
            'areaCode' => 'frontend',
            'baseController' => 'Magento\Core\Controller\Front\Action',
            'routerId' => 'standard'
        );
        $this->_model = \Mage::getModel('Magento\Core\Controller\Varien\Router\Base', $options);
        $this->_model->setFront(\Mage::getModel('Magento\Core\Controller\Varien\Front'));
    }

    public function testFetchDefault()
    {
        $default = array(
            'module' => 'core',
            'controller' => 'index',
            'action' => 'index'
        );
        $this->assertEmpty($this->_model->getFront()->getDefault());
        $this->_model->fetchDefault();
        $this->assertEquals($default, $this->_model->getFront()->getDefault());
    }

    public function testMatch()
    {
        if (!\Magento\TestFramework\Helper\Bootstrap::canTestHeaders()) {
            $this->markTestSkipped('Can\'t test get match without sending headers');
        }

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');

        $this->assertInstanceOf('Magento\Core\Controller\Varien\Action', $this->_model->match($request));
        $request->setRequestUri('core/index/index');
        $this->assertInstanceOf('Magento\Core\Controller\Varien\Action', $this->_model->match($request));

        $request->setPathInfo('not_exists/not_exists/not_exists')
            ->setModuleName('not_exists')
            ->setControllerName('not_exists')
            ->setActionName('not_exists');
        $this->assertNull($this->_model->match($request));
    }

    /**
     * @covers \Magento\Core\Controller\Varien\Router\Base::getModulesByFrontName
     * @covers \Magento\Core\Controller\Varien\Router\Base::getRouteByFrontName
     * @covers \Magento\Core\Controller\Varien\Router\Base::getFrontNameByRoute
     */
    public function testGetters()
    {
        $this->assertEquals(array('Magento_Catalog'), $this->_model->getModulesByFrontName('catalog'));
        $this->assertEquals('cms', $this->_model->getRouteByFrontName('cms'));
        $this->assertEquals('cms', $this->_model->getFrontNameByRoute('cms'));
    }

    public function testGetControllerClassName()
    {
        $this->assertEquals(
            'Magento\Core\Controller\Index',
            $this->_model->getControllerClassName('Magento_Core', 'index')
        );
    }
}
