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
class Magento_Core_Model_Page_Asset_Minified implements Magento_Core_Model_Page_Asset_MergeableInterface
{

    /**
     * @var Magento_Core_Model_Page_Asset_LocalInterface
     */
    protected $_originalAsset;

    /**
     * @var Magento_Code_Minifier
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
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Page_Asset_LocalInterface $asset
     * @param Magento_Code_Minifier $minifier
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Model_Page_Asset_LocalInterface $asset,
        Magento_Code_Minifier $minifier,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_Core_Model_Logger $logger
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
        } catch (Exception $e) {
            $this->_logger->logException(new Magento_Exception('Could not minify file: ' . $originalFile, 0, $e));
            $this->_file = $originalFile;
        }
        if ($this->_file == $originalFile) {
            $this->_url = $this->_originalAsset->getUrl();
        } else {
            $this->_url = $this->_viewUrl->getPublicFileUrl($this->_file);
        }
    }
}
