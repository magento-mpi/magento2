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
     * Minfier
     *
     * @var \Magento\Code\Minifier
     */
    protected $minifier;

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
     * Directory for static view directory
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
     * @param \Magento\Code\Minifier $minifier
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\UrlInterface $baseUrl
     */
    public function __construct(
        LocalInterface $asset,
        \Magento\Code\Minifier $minifier,
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem,
        \Magento\UrlInterface $baseUrl
    ) {
        $this->originalAsset = $asset;
        $this->minifier = $minifier;
        $this->logger = $logger;
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
     * Minify content of child asset
     *
     * @return void
     */
    protected function process()
    {
        $originalFile = $this->originalAsset->getSourceFile();

        try {
            $this->file = $this->minifier->getMinifiedFile($originalFile);
        } catch (\Exception $e) {
            $this->logger->logException(new \Magento\Exception('Could not minify file: ' . $originalFile, 0, $e));
            $this->file = $originalFile;
        }
        if ($this->file == $originalFile) {
            // Minifier says to use original file
            $this->relativePath = $this->originalAsset->getRelativePath();
            $this->url = $this->originalAsset->getUrl();
        } else if (dirname($this->file) == dirname($originalFile)) {
            // Minifier says to replace it with some other file in the same directory
            $baseName = basename($this->file);
            $originalBaseName = basename($originalFile);
            $this->relativePath = str_replace($originalBaseName, $baseName, $this->originalAsset->getRelativePath());
            $this->url = str_replace($originalBaseName, $baseName, $this->originalAsset->getUrl());
        } else {
            // Minifier generated a new file
            $this->relativePath = $this->staticViewDir->getRelativePath($this->file);
            $this->url = $this->baseUrl->getBaseUrl(array('_type' => \Magento\UrlInterface::URL_TYPE_STATIC))
                . $this->relativePath;
        }
    }
}
