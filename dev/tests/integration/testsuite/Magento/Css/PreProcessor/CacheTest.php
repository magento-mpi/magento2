<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    protected $model;

    /**
     * @var \Magento\Css\PreProcessor\Cache\CacheManagerFactory
     */
    protected $cacheManagerFactory;

    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create('Magento\Css\PreProcessor\Less');
        $this->cacheManagerFactory = $objectManager->create('Magento\Css\PreProcessor\Cache\CacheManagerFactory');
        $this->filesystem = $objectManager->get('Magento\Filesystem');
        $this->viewService = $objectManager->get('Magento\View\Service');

        $this->clearCache();

        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::PUB_LIB_DIR => array(
                    'path' => __DIR__ . '/_files/cache/lib'
                ),
            )
        ));
    }

    protected function tearDown()
    {
        $this->clearCache();
    }

    public function testProcess()
    {
        $sourceFilePath = 'oyejorge.less';

        $designParams = $this->getDesignParams();
        $targetDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::TMP_DIR);

        /**
         * cache was not initialize yet and will return empty value
         *
         * @var \Magento\Css\PreProcessor\Cache\CacheManager $cacheManagerEmpty
         */
        $cacheManagerEmpty = $this->cacheManagerFactory->create($sourceFilePath, $designParams);
        $this->assertEmpty($cacheManagerEmpty->getCachedFile());

        $this->model->process($sourceFilePath, $designParams, $targetDirectory);

        /**
         * cache initialized and will return cached file
         *
         * @var \Magento\Css\PreProcessor\Cache\CacheManager $cacheManagerGenerated
         */
        $cacheManagerGenerated = $this->cacheManagerFactory->create($sourceFilePath, $designParams);
        $this->assertNotEmpty($cacheManagerGenerated->getCachedFile());
    }

    /**
     * @return array
     */
    protected function getDesignParams()
    {
        $designParams = ['area' => 'frontend'];
        /** @var \Magento\View\Service $viewService */
        $this->viewService->updateDesignParams($designParams);

        return $designParams;
    }

    /**
     * @return $this
     */
    protected function clearCache()
    {
        /** @var \Magento\Filesystem\Directory\WriteInterface $mapsDirectory */
        $mapsDirectory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);

        if ($mapsDirectory->isDirectory(\Magento\Css\PreProcessor\Cache\Import\Map\Storage::MAPS_DIR)) {
            $mapsDirectory->delete(\Magento\Css\PreProcessor\Cache\Import\Map\Storage::MAPS_DIR);
        }
        return $this;
    }
}
