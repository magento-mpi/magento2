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

/**
 * Test class for Mage_Core_Model_Image_AdapterFactory
 * @magentoAppArea adminhtml
 */
class Mage_Core_Model_Image_AdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Image_AdapterFactory');
        $this->_config = Mage::getModel('Mage_Core_Model_Store_Config');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        $result = $this->_model->create();
        $this->assertInstanceOf('Magento_Image_Adapter_Abstract', $result);
        $this->assertNotEmpty($this->_config->getConfig(Mage_Core_Model_Image_AdapterFactory::XML_PATH_IMAGE_ADAPTER));
    }
}
