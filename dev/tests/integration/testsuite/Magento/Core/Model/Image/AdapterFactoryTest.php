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

/**
 * Test class for Magento_Core_Model_Image_AdapterFactory
 * @magentoAppArea adminhtml
 */
class Magento_Core_Model_Image_AdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Image_AdapterFactory');
        $this->_config = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Store_Config');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        $result = $this->_model->create();
        $this->assertInstanceOf('Magento_Image_Adapter_Abstract', $result);
        $this->assertNotEmpty(
            $this->_config->getConfig(Magento_Core_Model_Image_AdapterFactory::XML_PATH_IMAGE_ADAPTER)
        );
    }
}
