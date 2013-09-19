<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_RouterListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\RouterList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\Core\Model\RouterList');
    }

    public function testGetRouterByRoute()
    {
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\Base',
            $this->_model->getRouterByRoute('')
        );
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\Base',
            $this->_model->getRouterByRoute('checkout')
        );
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\DefaultRouter',
            $this->_model->getRouterByRoute('test')
        );
    }

    public function testGetRouterByFrontName()
    {
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\Base',
            $this->_model->getRouterByFrontName('')
        );
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\Base',
            $this->_model->getRouterByFrontName('checkout')
        );
        $this->assertInstanceOf(
            'Magento\Core\Controller\Varien\Router\DefaultRouter',
            $this->_model->getRouterByFrontName('test')
        );
    }
}
