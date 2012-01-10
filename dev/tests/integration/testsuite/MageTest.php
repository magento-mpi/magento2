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

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetConfigModel()
    {
        Mage::reset();
        Mage::setConfigModel();
        $config = Mage::getConfig();
        $this->assertInstanceOf('Mage_Core_Model_Config', $config);

        Mage::reset();
        Mage::setConfigModel(array('config_model' => 'Mage_Core_Model_Config'));
        $config = Mage::getConfig();
        $this->assertInstanceOf('Mage_Core_Model_Config', $config);

        Mage::reset();
        Mage::setConfigModel(array('config_model' => 'ERROR_STRING'));
        $config = Mage::getConfig();
        $this->assertInstanceOf('Mage_Core_Model_Config', $config);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetIsInstalled()
    {
        if (!Mage::isInstalled()) {
            $this->assertFalse(Mage::isInstalled());

            Mage::setIsInstalled(array('is_installed' => true));
            $this->assertTrue(Mage::isInstalled());
        }
    }
}
