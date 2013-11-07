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
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCreate()
    {
        /** @var AdapterFactory $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Image\AdapterFactory');
        /** @var \Magento\Core\Model\ConfigInterface $config */
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\ConfigInterface');

        $result = $model->create();
        $this->assertInstanceOf('Magento\Image\Adapter\AbstractAdapter', $result);
        $this->assertNotEmpty(
            $config->getValue(\Magento\Core\Model\Image\Adapter\Config::XML_PATH_IMAGE_ADAPTER)
        );
    }
}
