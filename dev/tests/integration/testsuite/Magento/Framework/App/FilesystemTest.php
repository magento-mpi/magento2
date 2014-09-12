<?php
/**
 * Test for \Magento\Framework\App\Filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

/**
 * Class FilesystemTest
 * Test for Magento\Framework\App\Filesystem class
 *
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Framework\App\Filesystem');
    }

    /**
     * Test getPath returns right path
     */
    public function testGetPath()
    {
        $this->assertContains('design', $this->filesystem->getPath(\Magento\Framework\App\Filesystem::THEMES_DIR));
    }
}
