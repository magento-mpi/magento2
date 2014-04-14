<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Css\PreProcessor;

use Magento\Css\PreProcessor\Cache\Import\Cache;
use Magento\Css\PreProcessor\Cache\Import\Map\Storage;

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
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->preProcessorLess = $this->objectManager->create('Magento\Css\PreProcessor\Less');
        $this->filesystem = $this->objectManager->get('Magento\Framework\Filesystem');

        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
            array(
                \Magento\Framework\App\Filesystem::PARAM_APP_DIRS => array(
                    \Magento\Framework\App\Filesystem::PUB_LIB_DIR => array('path' => __DIR__ . '/_files/cache/lib')
                )
            )
        );

        $this->clearCache();
    }

    protected function tearDown()
    {
        $this->clearCache();
    }

    public function testLessCache()
    {
        $file = $this->objectManager->create(
            'Magento\View\Publisher\CssFile',
            array('filePath' => 'oyejorge.css', 'allowDuplication' => false, 'viewParams' => $this->getDesignParams())
        );

        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::TMP_DIR);

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
        $designParams = array('area' => 'frontend');
        $viewService = $this->objectManager->get('Magento\View\Service');
        $viewService->updateDesignParams($designParams);

        return $designParams;
    }

    /**
     * @return $this
     */
    protected function clearCache()
    {
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface $mapsDirectory */
        $mapsDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);

        if ($mapsDirectory->isDirectory(Storage::MAPS_DIR)) {
            $mapsDirectory->delete(Storage::MAPS_DIR);
        }
        return $this;
    }
}
