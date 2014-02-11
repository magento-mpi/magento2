<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sub directory in library
     */
    const LIB_SUB_DIR = 'less_cache';

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $tmpDirectory;

    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    protected $preprocessorLess;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-18229');
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->filesystem = $this->objectManager->get('Magento\Filesystem');
        $this->objectManager->get('Magento\App\State')->setAreaCode('frontend');
        /** @var \Magento\Css\PreProcessor\Less $less */
        $this->preprocessorLess = $this->objectManager->create('Magento\Css\PreProcessor\Less');
        $this->tmpDirectory = $this->objectManager->create('Magento\App\Filesystem')
            ->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR);

        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::PUB_LIB_DIR => array(
                    'path' => $this->tmpDirectory->getAbsolutePath() . '/' . self::LIB_SUB_DIR
                ),
            )
        ));
    }

    /**
     * @param array $content
     * @dataProvider contentProvider
     */
    public function testCacheGeneration($content)
    {
        $designParams = array('area' => 'frontend');
        /** @var \Magento\View\Service $viewService */
        $viewService = $this->objectManager->get('Magento\View\Service');
        $viewService->updateDesignParams($designParams);

        /**
         * Validate that content will be changed only when file will be edited (mtime changed)
         */
        foreach ($content as $testData) {
            $cachedContent = $this->processLess($designParams, $testData['content'], $testData['mtime']);
            $this->assertContains($testData['expected'], $cachedContent);
        }
    }

    /**
     * @param array $designParams
     * @param string $content
     * @param int $mtime
     * @return string
     */
    protected function processLess($designParams, $content, $mtime)
    {
        if (!$this->tmpDirectory->isDirectory(self::LIB_SUB_DIR)) {
            $this->tmpDirectory->create(self::LIB_SUB_DIR);
        }

        $this->tmpDirectory->writeFile(self::LIB_SUB_DIR . '/test_main.less', '@import "test_import.less";');
        $this->tmpDirectory->writeFile(self::LIB_SUB_DIR . '/test_import.less', $content);
        $this->tmpDirectory->touch(self::LIB_SUB_DIR . '/test_import.less', $mtime);

        $publishedFile = $this->preprocessorLess->process(
            'test_main.less', $designParams, $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR)
        );

        return $this->tmpDirectory->readFile($this->tmpDirectory->getRelativePath($publishedFile));
    }

    /**
     * @return array
     */
    public function contentProvider()
    {
        $currentTime = time();

        return [[[
            [
                'content'  => 'h1 { background-color: red; }',
                'mtime'    => $currentTime,
                'expected' => 'background-color: red;'
            ],
            [
                'content'  => 'h1 { background-color: green; }',
                'mtime'    => $currentTime,
                'expected' => 'background-color: red;'
            ],
            [
                'content'  => 'h1 { background-color: blue; }',
                'mtime'    => $currentTime + 1,
                'expected' => 'background-color: blue;'
            ]
        ]]];
    }
}
