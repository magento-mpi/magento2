<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Asset;

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
     * @var \Magento\Framework\Code\Minifier
     */
    protected $minifier;

    /**
     * File
     *
     * @var string
     */
    protected $file;

    /**
     * URL
     *
     * @var string
     */
    protected $url;

    /**
     * View URL
     *
     * @var \Magento\Framework\View\Url
     */
    protected $viewUrl;

    /**
     * Logger
     *
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param LocalInterface $asset
     * @param \Magento\Framework\Code\Minifier $minifier
     * @param \Magento\Framework\View\Url $viewUrl
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        LocalInterface $asset,
        \Magento\Framework\Code\Minifier $minifier,
        \Magento\Framework\View\Url $viewUrl,
        \Magento\Framework\Logger $logger
    ) {
        $this->originalAsset = $asset;
        $this->minifier = $minifier;
        $this->viewUrl = $viewUrl;
        $this->logger = $logger;
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
            $this->logger->logException(new \Magento\Framework\Exception('Could not minify file: ' . $originalFile, 0, $e));
            $this->file = $originalFile;
        }
        if ($this->file == $originalFile) {
            $this->url = $this->originalAsset->getUrl();
        } else {
            $this->url = $this->viewUrl->getPublicFileUrl($this->file);
        }
    }
}
