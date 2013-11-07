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

namespace Magento\Image;

/**
 * Test class for \Magento\Image\AdapterFactory
 * @magentoAppArea adminhtml
 */
class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Image\AdapterFactory');
        $this->_config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\ConfigInterface');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        $result = $this->_model->create();
        $this->assertInstanceOf('Magento\Image\Adapter\AbstractAdapter', $result);
        $this->assertNotEmpty(
            $this->_config->getNode(\Magento\Core\Model\Image\Adapter\Config::XML_PATH_IMAGE_ADAPTER)
        );
    }
}
