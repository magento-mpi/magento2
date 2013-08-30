<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_RouterListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_RouterList
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_model = $this->_objectManager->create('Mage_Core_Model_RouterList');
    }

    public function testGetRouterByRoute()
    {
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Router_Base', $this->_model->getRouterByRoute(''));
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Router_Base', $this->_model->getRouterByRoute('checkout'));
        $this->assertInstanceOf('Mage_Core_Controller_Varien_Router_Default', $this->_model->getRouterByRoute('test'));
    }

    public function testGetRouterByFrontName()
    {
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('')
        );
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Base',
            $this->_model->getRouterByFrontName('checkout')
        );
        $this->assertInstanceOf(
            'Mage_Core_Controller_Varien_Router_Default',
            $this->_model->getRouterByFrontName('test')
        );
    }
}
