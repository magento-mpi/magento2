<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_App
 */
class Mage_Core_Model_AppTest extends PHPUnit_Framework_TestCase
{
    /*
     * Test layout class instance
     */
    const LAYOUT_INSTANCE = 'TestLayoutInstance';

    /**
     * @var Mage_Core_Model_App
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $frontController = $this->getMock('Mage_Core_Controller_Varien_Front', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento_ObjectManager_Zend', array('get'), array(), '', false);
        $this->_model = new Mage_Core_Model_App($frontController, $this->_objectManager);
    }

    public function testGetLayout()
    {
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Mage_Core_Model_Layout')
            ->will($this->returnValue(self::LAYOUT_INSTANCE));

        $this->assertEquals(self::LAYOUT_INSTANCE, $this->_model->getLayout());
    }
}
