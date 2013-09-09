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
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_RouterList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento_Core_Model_RouterList');
    }

    public function testGetRouterByRoute()
    {
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByRoute('')
        );
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByRoute('checkout')
        );
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Default',
            $this->_model->getRouterByRoute('test')
        );
    }

    public function testGetRouterByFrontName()
    {
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('')
        );
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('checkout')
        );
        $this->assertInstanceOf(
            'Magento_Core_Controller_Varien_Router_Default',
            $this->_model->getRouterByFrontName('test')
        );
    }
}
