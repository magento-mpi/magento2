<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

use \Magento\Css\PreProcessor\Cache\Import\Cache;
use \Magento\Css\PreProcessor\Cache\Import\Map\Storage;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    protected $preProcessorLess;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->preProcessorLess = $this->objectManager->create('Magento\Css\PreProcessor\Less');
        $this->filesystem = $this->objectManager->get('Magento\Filesystem');

        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::LIB_WEB => array(
                    'path' => __DIR__ . '/_files/cache/lib'
                ),
            )
        ));

        $this->clearCache();
    }

    protected function tearDown()
    {
        $this->clearCache();
    }

    public function testLessCache()
    {
        $this->markTestIncomplete(
            'Implement MAGETWO-21933 LESS Preprocessing Through \Magento\View\Asset\PreProcessorInterface'
        );

        $file = $this->objectManager->create('Magento\View\Publisher\CssFile',
            [
                'filePath' => 'oyejorge.css',
                'viewParams' => $this->getDesignParams()
            ]
        );

        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR);

        /**
         * cache was not initialize yet and return empty value
         *
         * @var \Magento\Css\PreProcessor\Cache\CacheManager $cacheManagerEmpty
         */
        $emptyCache = $this->objectManager->create('Magento\Css\PreProcessor\Cache\CacheManager');
        $emptyCache->initializeCacheByType(Cache::IMPORT_CACHE, $file);
        $this->assertEmpty($emptyCache->getCachedFile(Cache::IMPORT_CACHE));

        $this->preProcessorLess->process($file, $targetDirectory);

        /**
         * cache initialized and return cached file
         *
         * @var \Magento\Css\PreProcessor\Cache\CacheManager $cacheManagerGenerated
         */
        $generatedCache = $this->objectManager->create('Magento\Css\PreProcessor\Cache\CacheManager');
        $generatedCache->initializeCacheByType(Cache::IMPORT_CACHE, $file);
        $this->assertNotEmpty($generatedCache->getCachedFile(Cache::IMPORT_CACHE));
    }

    /**
     * @return array
     */
    protected function getDesignParams()
    {
        $designParams = ['area' => 'frontend'];
        /** @var \Magento\View\Asset\Service $assetService */
        $assetService = $this->objectManager->get('Magento\View\Asset\Service');
        $assetService->updateDesignParams($designParams);

        return $designParams;
    }

    /**
     * @return $this
     */
    protected function clearCache()
    {
        /** @var \Magento\Filesystem\Directory\WriteInterface $mapsDirectory */
        $mapsDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

        if ($mapsDirectory->isDirectory(Storage::MAPS_DIR)) {
            $mapsDirectory->delete(Storage::MAPS_DIR);
        }
        return $this;
    }
}
