<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html\Header;

/**
 * Logo page header block
 */
class Logo extends \Magento\Framework\View\Element\Template
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'html/header/logo.phtml';

    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_fileStorageHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\File\Storage\Database $fileStorageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\File\Storage\Database $fileStorageHelper,
        array $data = array()
    ) {
        $this->_fileStorageHelper = $fileStorageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', array('_current' => true));
        $urlRewrite = $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
        return $currentUrl == $urlRewrite;
    }

    /**
     * Get logo image URL
     *
     * @return string
     */
    public function getLogoSrc()
    {
        if (empty($this->_data['logo_src'])) {
            $this->_data['logo_src'] = $this->_getLogoUrl();
        }
        return $this->_data['logo_src'];
    }

    /**
     * Retrieve logo text
     *
     * @return string
     */
    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = $this->_scopeConfig->getValue(
                'design/header/logo_alt',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return $this->_data['logo_alt'];
    }

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getLogoUrl()
    {
        $folderName = \Magento\Backend\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(array('_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA)) . $path;

        if (!is_null($storeLogoPath) && $this->_isFile($path)) {
            $url = $logoUrl;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.gif');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative path
     * @return bool
     */
    protected function _isFile($filename)
    {
        if ($this->_fileStorageHelper->checkDbUsage() && !$this->getMediaDirectory()->isFile($filename)) {
            $this->_fileStorageHelper->saveFileToFilesystem($filename);
        }

        return $this->getMediaDirectory()->isFile($filename);
    }
}
