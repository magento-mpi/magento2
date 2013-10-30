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

namespace Magento\Core\App\Router;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\Router\Base
     */
    protected $_model;

    protected function setUp()
    {
        $options = array(
            'areaCode' => 'frontend',
            'baseController' => 'Magento\Core\Controller\Front\Action',
            'routerId' => 'standard'
        );
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\App\Router\Base', $options);
        $this->_model->setFront(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\FrontController'));
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

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');

        $this->assertInstanceOf('Magento\Core\Controller\Varien\Action', $this->_model->match($request));
        $request->setRequestUri('core/index/index');
        $this->assertInstanceOf('Magento\Core\Controller\Varien\Action', $this->_model->match($request));

        $request->setPathInfo('not_exists/not_exists/not_exists')
            ->setModuleName('not_exists')
            ->setControllerName('not_exists')
            ->setActionName('not_exists');
        $this->assertNull($this->_model->match($request));
    }

    public function testGetControllerClassName()
    {
        $this->assertEquals(
            'Magento\Core\Controller\Index',
            $this->_model->getControllerClassName('Magento_Core', 'index')
        );
    }
}
