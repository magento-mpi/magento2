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
 * Test class for \Magento\Core\Model\Image\AdapterFactory
 * @magentoAppArea adminhtml
 */
class Magento_Core_Model_Image_AdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = Mage::getModel('Magento\Core\Model\Image\AdapterFactory');
        $this->_config = Mage::getModel('Magento\Core\Model\Store\Config');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        $result = $this->_model->create();
        $this->assertInstanceOf('\Magento\Image\Adapter\AbstractAdapter', $result);
        $this->assertNotEmpty(
            $this->_config->getConfig(\Magento\Core\Model\Image\AdapterFactory::XML_PATH_IMAGE_ADAPTER)
        );
    }
}
