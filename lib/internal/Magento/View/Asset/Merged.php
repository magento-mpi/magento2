<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * \Iterator that aggregates one or more assets and provides a single public file with equivalent behavior
 */
class Merged implements \Iterator
{
    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var MergeStrategyInterface
     */
    protected $mergeStrategy;

    /**
     * @var \Magento\App\Filesystem
     */
    private $appFileSystem;

    /**
     * @var \Magento\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var MergeableInterface[]
     */
    protected $assets;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * @param \Magento\Logger $logger
     * @param MergeStrategyInterface $mergeStrategy
     * @param \Magento\App\Filesystem $appFileSystem
     * @param \Magento\View\Asset\Repository $assetRepo
     * @param array $assets
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Logger $logger,
        MergeStrategyInterface $mergeStrategy,
        \Magento\App\Filesystem $appFileSystem,
        \Magento\View\Asset\Repository $assetRepo,
        array $assets
    ) {
        $this->logger = $logger;
        $this->mergeStrategy = $mergeStrategy;
        $this->appFileSystem = $appFileSystem;
        $this->assetRepo = $assetRepo;

        if (!$assets) {
            throw new \InvalidArgumentException('At least one asset has to be passed for merging.');
        }
        /** @var $asset MergeableInterface */
        foreach ($assets as $asset) {
            if (!($asset instanceof MergeableInterface)) {
                throw new \InvalidArgumentException(
                    'Asset has to implement \Magento\View\Asset\MergeableInterface.'
                );
            }
            if (!$this->contentType) {
                $this->contentType = $asset->getContentType();
            } else if ($asset->getContentType() != $this->contentType) {
                throw new \InvalidArgumentException(
                    "Content type '{$asset->getContentType()}' cannot be merged with '{$this->contentType}'."
                );
            }
        }
        $this->assets = $assets;
    }

    /**
     * Attempt to merge assets, falling back to original non-merged ones, if merging fails
     *
     * @return void
     */
    protected function initialize()
    {
        if (!$this->isInitialized) {
            $this->isInitialized = true;
            try {
                $mergedAsset = $this->createMergedAsset($this->assets);
                $this->mergeStrategy->merge($this->assets, $mergedAsset);
                $this->assets = array($mergedAsset);
            } catch (\Exception $e) {
                $this->logger->logException($e);
            }
        }
    }

    /**
     * Create an asset object for merged file
     *
     * @param array $assets
     * @return FileId
     */
    private function createMergedAsset(array $assets)
    {
        $paths = array();
        /** @var MergeableInterface $asset */
        foreach ($assets as $asset) {
            $paths[] = $asset->getRelativePath();
        }
        $paths = array_unique($paths);
        $filePath = self::getRelativeDir() . '/' . md5(implode('|', $paths)) . '.' . $this->contentType;
        $sourceFile = $this->appFileSystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR) . '/' . $filePath;
        return $this->assetRepo->createFileAsset($filePath, $sourceFile);
    }

    /**
     * {@inheritdoc}
     *
     * @return AssetInterface
     */
    public function current()
    {
        $this->initialize();
        return current($this->assets);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        $this->initialize();
        return key($this->assets);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->initialize();
        next($this->assets);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->initialize();
        reset($this->assets);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        $this->initialize();
        return (bool)current($this->assets);
    }

    /**
     * Returns directory for storing merged files relative to STATIC_VIEW_DIR
     *
     * @return string
     */
    public static function getRelativeDir()
    {
        return \Magento\App\Filesystem\DirectoryList::CACHE_VIEW_REL_DIR . '/merged';
    }
}
