<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class MageTest extends PHPUnit_Framework_TestCase
{
    public function testIsInstalled()
    {
        $this->assertTrue(Mage::isInstalled());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testReset()
    {
        Mage::setRoot(__DIR__);
        $this->assertNotNull(Mage::getRoot());
        Mage::reset();
        $this->assertNull(Mage::getRoot());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDesign()
    {
        $design = Mage::getDesign();
        $this->assertEquals('frontend', $design->getArea());
        $this->assertSame(Mage::getDesign(), $design);
    }

    public function testGetModel()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', Mage::getModel('core/config'));
        $this->assertInstanceOf('Mage_Core_Model_Config', Mage::getModel('Mage_Core_Model_Config'));
    }

    public function testGetResourceModel()
    {
        $this->assertInstanceOf('Mage_Core_Model_Resource_Config', Mage::getResourceModel('core/config'));
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config', Mage::getResourceModel('Mage_Core_Model_Resource_Config')
        );
    }

    public function testHelper()
    {
        $this->assertInstanceOf('Mage_Core_Helper_Data', Mage::helper('core'));
        $this->assertInstanceOf('Mage_Core_Helper_Http', Mage::helper('core/http'));
        $this->assertInstanceOf('Mage_Core_Helper_Js', Mage::helper('Mage_Core_Helper_Js'));
    }
}
