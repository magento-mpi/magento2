<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\File;

use Magento\Framework\App\Filesystem\DirectoryList;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test for \Magento\Core\Model\File\Storage::getScriptConfig()
     *
     * @magentoConfigFixture current_store system/media_storage_configuration/configuration_update_time 1000
     */
    public function testGetScriptConfig()
    {
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\File\Storage'
        )->getScriptConfig();
        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('media_directory', $config);
        $this->assertArrayHasKey('allowed_resources', $config);
        $this->assertArrayHasKey('update_time', $config);
        $this->assertEquals(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Framework\App\Filesystem'
            )->getPath(
                    DirectoryList::MEDIA
            ),
            $config['media_directory']
        );
        $this->assertInternalType('array', $config['allowed_resources']);
        $this->assertContains('css', $config['allowed_resources']);
        $this->assertContains('css_secure', $config['allowed_resources']);
        $this->assertContains('js', $config['allowed_resources']);
        $this->assertContains('theme', $config['allowed_resources']);
        $this->assertEquals(1000, $config['update_time']);
    }
}
