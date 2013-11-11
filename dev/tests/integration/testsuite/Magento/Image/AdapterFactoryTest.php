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
     * @magentoConfigFixture dev/image/default_adapter GD2
     */
    public function testCreateGD2()
    {
        /** @var AdapterFactory $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Image\AdapterFactory');
        /** @var \Magento\Core\Model\ConfigInterface $config */
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\ConfigInterface');

        $result = $model->create();
        $this->assertInstanceOf('Magento\Image\Adapter\Gd2', $result);
        $this->assertEquals(
            'GD2',
            $config->getValue(\Magento\Core\Model\Image\Adapter\Config::XML_PATH_IMAGE_ADAPTER)
        );
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoConfigFixture dev/image/default_adapter IMAGEMAGICK
     */
    public function testCreateImageMagick()
    {
        /** @var AdapterFactory $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Image\AdapterFactory');
        /** @var \Magento\Core\Model\ConfigInterface $config */
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\ConfigInterface');

        $result = $model->create();
        $this->assertInstanceOf('Magento\Image\Adapter\ImageMagick', $result);
        $this->assertEquals(
            'IMAGEMAGICK',
            $config->getValue(\Magento\Core\Model\Image\Adapter\Config::XML_PATH_IMAGE_ADAPTER)
        );
    }
}
