<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minified page asset
 */
namespace Magento\Core\Model\Page\Asset;

class Minified implements \Magento\Core\Model\Page\Asset\MergeableInterface
{

    /**
     * @var \Magento\Core\Model\Page\Asset\LocalInterface
     */
    protected $_originalAsset;

    /**
     * @var \Magento\Code\Minifier
     */
    protected $_minifier;

    /**
     * @var string
     */
    protected $_file;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var \Magento\Core\Model\View\Url
     */
    protected $_viewUrl;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Model\Page\Asset\LocalInterface $asset
     * @param \Magento\Code\Minifier $minifier
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\Core\Model\Logger $logger
     */
    public function __construct(
        \Magento\Core\Model\Page\Asset\LocalInterface $asset,
        \Magento\Code\Minifier $minifier,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\Core\Model\Logger $logger
    ) {
        $this->_originalAsset = $asset;
        $this->_minifier = $minifier;
        $this->_viewUrl = $viewUrl;
        $this->_logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if (empty($this->_url)) {
            $this->_process();
        }
        return $this->_url;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->_originalAsset->getContentType();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceFile()
    {
        if (empty($this->_file)) {
            $this->_process();
        }
        return $this->_file;
    }

    /**
     * Minify content of child asset
     */
    protected function _process()
    {
        $originalFile = $this->_originalAsset->getSourceFile();

        try {
            $this->_file = $this->_minifier->getMinifiedFile($originalFile);
        } catch (\Exception $e) {
            $this->_logger->logException(new \Magento\Exception('Could not minify file: ' . $originalFile, 0, $e));
            $this->_file = $originalFile;
        }
        if ($this->_file == $originalFile) {
            $this->_url = $this->_originalAsset->getUrl();
        } else {
            $this->_url = $this->_viewUrl->getPublicFileUrl($this->_file);
        }
    }
}
