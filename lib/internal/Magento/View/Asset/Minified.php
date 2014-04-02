<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * Minified page asset
 */
class Minified implements MergeableInterface
{
    /**
     * LocalInterface
     *
     * @var LocalInterface
     */
    protected $originalAsset;

    /**
     * Minification strategy
     *
     * @var \Magento\Code\Minifier\StrategyInterface
     */
    protected $strategy;

    /**
     * File
     *
     * @var string
     */
    protected $file;

    /**
     * Relative path to the file
     *
     * @var string
     */
    protected $relativePath;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var \Magento\View\Asset\File\Context
     */
    protected $context;

    /**
     * URL
     *
     * @var string
     */
    protected $url;

    /**
     * Logger
     *
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * Directory object for root directory
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $rootDir;

    /**
     * Directory object for static view directory
     *
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    protected $staticViewDir;

    /**
     * Url configuration
     *
     * @var \Magento\UrlInterface
     */
    protected $baseUrl;

    /**
     * Constructor
     *
     * @param LocalInterface $asset
     * @param \Magento\Code\Minifier\StrategyInterface $strategy
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     */
    public function __construct(
        LocalInterface $asset,
        \Magento\Code\Minifier\StrategyInterface $strategy,
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl
    ) {
        $this->originalAsset = $asset;
        $this->strategy = $strategy;
        $this->logger = $logger;
        $this->rootDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::ROOT_DIR);
        $this->staticViewDir = $filesystem->getDirectoryRead(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $this->baseUrl = $baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if (empty($this->url)) {
            $this->process();
        }
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->originalAsset->getContentType();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        if (empty($this->file)) {
            $this->process();
        }
        return $this->file;
    }

    /**
     * @inheritdoc
     */
    public function getRelativePath()
    {
        if (empty($this->relativePath)) {
            $this->process();
        }
        return $this->relativePath;
    }

    /**
     * @inheritdoc
     */
    public function getFilePath()
    {
        if (null === $this->filePath) {
            $this->process();
        }
        return $this->filePath;
    }

    /**
     * @inheritdoc
     */
    public function getContext()
    {
        if (null === $this->context) {
            $this->process();
        }
        return $this->context;
    }

    /**
     * Minify content of child asset
     *
     * @return void
     */
    protected function process()
    {
        if ($this->isFileMinified($this->originalAsset->getRelativePath())) {
            $this->fillPropertiesByOriginalAsset();
        } else if ($this->hasPreminifiedFile($this->originalAsset->getSourceFile())) {
            $this->fillPropertiesByOriginalAssetWithMin();
        } else {
            try {
                $this->fillPropertiesByMinifyingAsset();
            } catch (\Exception $e) {
                $this->logger->logException(
                    new \Magento\Exception('Could not minify file: ' . $this->originalAsset->getSourceFile(), 0, $e)
                );
                $this->fillPropertiesByOriginalAsset();
            }
        }
    }

    /**
     * Check, whether file is already minified
     *
     * @param string $fileName
     * @return bool
     */
    protected function isFileMinified($fileName)
    {
        return (bool)preg_match('#.min.\w+$#', $fileName);
    }

    /**
     * Check, whether the file has its preminified version in the same directory
     *
     * @param $fileName
     * @return bool
     */
    protected function hasPreminifiedFile($fileName)
    {
        $minifiedFile = $this->composeMinifiedName($fileName);
        return $this->rootDir->isExist($this->rootDir->getRelativePath($minifiedFile));
    }

    /**
     * Compose path to a preminified file in the same folder out of path to an original file
     *
     * @param string $fileName
     * @return string
     */
    protected function composeMinifiedName($fileName)
    {
        return preg_replace('/\\.([^.]*)$/', '.min.$1', $fileName);
    }

    /**
     * Fill the properties by bare copying properties from original asset
     */
    protected function fillPropertiesByOriginalAsset()
    {
        $this->file = $this->originalAsset->getSourceFile();
        $this->relativePath = $this->originalAsset->getRelativePath();
        $this->filePath = $this->originalAsset->getFilePath();
        $this->context = $this->originalAsset->getContext();
        $this->url = $this->originalAsset->getUrl();
    }

    /**
     * Fill the properties by copying properties from original asset and adding '.min' inside them
     */
    protected function fillPropertiesByOriginalAssetWithMin()
    {
        $this->file = $this->composeMinifiedName($this->originalAsset->getSourceFile());
        $this->relativePath = $this->composeMinifiedName($this->originalAsset->getRelativePath());
        $this->filePath = $this->composeMinifiedName($this->originalAsset->getFilePath());
        $this->context = $this->originalAsset->getContext();
        $this->url = $this->composeMinifiedName($this->originalAsset->getUrl());
    }

    /**
     * Generate minified file and fill the properties to reference that file
     */
    protected function fillPropertiesByMinifyingAsset()
    {
        $originalFile = $this->originalAsset->getSourceFile();
        $originalFileRelRoot = $this->rootDir->getRelativePath($originalFile);
        $origRelativePath = $this->originalAsset->getRelativePath();

        $this->context = new \Magento\View\Asset\File\Context(
            $this->baseUrl->getBaseUrl(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC)),
            \Magento\App\Filesystem::STATIC_VIEW_DIR,
            \Magento\App\Filesystem\DirectoryList::CACHE_VIEW_REL_DIR . '/minified'
        );
        $this->filePath = md5($origRelativePath) . '_' . $this->composeMinifiedName(basename($origRelativePath));
        $this->relativePath = $this->context->getPath() . '/' . $this->filePath;
        $this->strategy->minifyFile($originalFileRelRoot, $this->relativePath);
        $this->file = $this->staticViewDir->getAbsolutePath($this->relativePath);
        $this->url = $this->context->getBaseUrl() . $this->relativePath;
    }
}
