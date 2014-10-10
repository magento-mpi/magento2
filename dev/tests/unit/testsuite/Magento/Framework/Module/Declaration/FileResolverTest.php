<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Module\Declaration;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for get method
     *
     * @dataProvider providerGet
     * @param $baseDir
     * @param $file
     * @param $scope
     * @param $expectedFileList
     */
    public function testGet($baseDir, $file, $scope, $expectedFileList)
    {
        $fileResolver = $this->getFileResolver($baseDir);

        $fileIterator = $fileResolver->get($file, $scope);
        $fileList = array();
        foreach ($fileIterator as $filePath) {
            $fileList[] = $filePath;
        }
        $this->assertEquals(sort($fileList), sort($expectedFileList));
    }

    /**
     * Data provider for testGet
     *
     * @return array
     */
    public function providerGet()
    {
        return array(
            array(
                __DIR__ . '/FileResolver/_files',
                'module.xml',
                'global',
                array(
                    file_get_contents(__DIR__ . '/FileResolver/_files/app/code/Module/Four/etc/module.xml'),
                    file_get_contents(__DIR__ . '/FileResolver/_files/app/code/Module/One/etc/module.xml'),
                    file_get_contents(__DIR__ . '/FileResolver/_files/app/code/Module/Three/etc/module.xml'),
                    file_get_contents(__DIR__ . '/FileResolver/_files/app/code/Module/Two/etc/module.xml'),
                    file_get_contents(__DIR__ . '/FileResolver/_files/app/etc/custom/module.xml')
                )
            )
        );
    }

    /**
     * Get file resolver instance
     *
     * @param string $baseDir
     * @return FileResolver
     */
    protected function getFileResolver($baseDir)
    {
        $driverPool = new \Magento\Framework\Filesystem\DriverPool;
        $filesystem = new \Magento\Framework\App\Filesystem(
            new \Magento\Framework\App\Filesystem\DirectoryList($baseDir),
            new \Magento\Framework\Filesystem\Directory\ReadFactory($driverPool),
            new \Magento\Framework\Filesystem\Directory\WriteFactory($driverPool)
        );
        $iteratorFactory = new \Magento\Framework\Config\FileIteratorFactory();

        return new \Magento\Framework\Module\Declaration\FileResolver($filesystem, $iteratorFactory);
    }
}
