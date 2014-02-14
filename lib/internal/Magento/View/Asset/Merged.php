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
     * Sub path for merged files relative to public view cache directory
     */
    const PUBLIC_MERGE_DIR  = '_merged';

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var MergeStrategyInterface
     */
    protected $mergeStrategy;

    /**
     * @var MergeableInterface[]
     */
    protected $assets;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * Whether initialization has been performed or not
     *
     * @var bool
     */
    protected $isInitialized = false;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Logger $logger
     * @param MergeStrategyInterface $mergeStrategy
     * @param array $assets
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Logger $logger,
        MergeStrategyInterface $mergeStrategy,
        array $assets
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->mergeStrategy = $mergeStrategy;

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
     */
    protected function initialize()
    {
        if (!$this->isInitialized) {
            $this->isInitialized = true;
            try {
                $this->assets = array($this->getMergedAsset($this->assets));
            } catch (\Exception $e) {
                $this->logger->logException($e);
            }
        }
    }

    /**
     * Retrieve asset instance representing a merged file
     *
     * @param MergeableInterface[] $assets
     * @return AssetInterface
     */
    protected function getMergedAsset(array $assets)
    {
        $sourceFiles = $this->getPublicFilesToMerge($assets);
        $destinationFile = $this->getMergedFilePath($sourceFiles);

        $this->mergeStrategy->mergeFiles($sourceFiles, $destinationFile, $this->contentType);
        return $this->objectManager->create('Magento\View\Asset\PublicFile', array(
            'file' => $destinationFile,
            'contentType' => $this->contentType,
        ));
    }

    /**
     * Go through all the files to merge, ensure that they are public (publish if needed), and compose
     * array of public paths to merge
     *
     * @param MergeableInterface[] $assets
     * @return array
     */
    protected function getPublicFilesToMerge(array $assets)
    {
        $result = array();
        foreach ($assets as $asset) {
            $publicFile = $asset->getSourceFile();
            $result[$publicFile] = $publicFile;
        }
        return $result;
    }

    /**
     * Return file name for the resulting merged file
     *
     * @param array $publicFiles
     * @return string
     */
    protected function getMergedFilePath(array $publicFiles)
    {
        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = $this->objectManager->get('Magento\App\Filesystem');
        $publicDir = $filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR);

        $relFileNames = array();
        foreach ($publicFiles as $file) {
            $relFileNames[] = ltrim(str_replace($publicDir, '', $file), '/');
        }

        $mergedDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::PUB_VIEW_CACHE_DIR)
            ->getAbsolutePath(self::PUBLIC_MERGE_DIR);
        return $mergedDir . '/' . md5(implode('|', $relFileNames)) . '.' . $this->contentType;
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
}
