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

class Mage_Core_Model_Image_AdapterFactoryTest extends Mage_Backend_Area_TestCase
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
        $this->assertInstanceOf('Varien_Image_Adapter_Abstract', $result);
        $this->assertNotEmpty($this->_config->getConfig(Mage_Core_Model_Image_AdapterFactory::XML_PATH_IMAGE_ADAPTER));
    }
}
