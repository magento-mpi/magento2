<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Declaration\FileResolver;

class ComposerJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for get method
     */
    public function testGet()
    {
        $mockModuleRoot = __DIR__ . '/_files';
        $mockModules = ['module1', 'module2', 'module3'];
        $fileName = 'composer.json';
        foreach($mockModules as $module) {
            $expectedFileList[] = $mockModuleRoot . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $fileName;
        }

        $composerJson = $this->getComposerJson($mockModuleRoot);

        /** file and scope arguments exist to satisfy interface, not used in method*/
        $fileIterator = $composerJson->get('file argument not used', 'scope argument not used');

        $this->assertCount(count($mockModules), $fileIterator);
        $i = 0;
        foreach ($fileIterator as $fileContent) {
            $this->assertContains($mockModules[$i], $fileContent);
            $i++;
        }
    }

    /**
     * Get file resolver instance
     *
     * @param string $baseDir
     * @return ComposerJson
     */
    protected function getComposerJson($baseDir)
    {
        $driverPool = new \Magento\Framework\Filesystem\DriverPool;
        $filesystem = new \Magento\Framework\Filesystem(
            new \Magento\Framework\App\Filesystem\DirectoryList($baseDir),
            new \Magento\Framework\Filesystem\Directory\ReadFactory($driverPool),
            new \Magento\Framework\Filesystem\Directory\WriteFactory($driverPool)
        );
        $iteratorFactory = new \Magento\Framework\Config\FileIteratorFactory();

        return new \Magento\Framework\Module\Declaration\FileResolver\ComposerJson($filesystem, $iteratorFactory);
    }
}
