<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

/**
 * Html page header block
 */
class Header extends \Magento\View\Element\Template
{
    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'html/header.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function getIsHomePage()
    {
        return $this->getUrl('', array('_current' => true)) == $this->getUrl(
            '*/*/*',
            array('_current' => true, '_use_rewrite' => true)
        );
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
            $this->_data['logo_alt'] = $this->_storeConfig->getConfig('design/header/logo_alt');
        }
        return $this->_data['logo_alt'];
    }

    /**
     * Retrieve welcome text
     *
     * @return string
     */
    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if ($this->_appState->isInstalled() && $this->_customerSession->isLoggedIn()) {
                $this->_data['welcome'] = __('Welcome, %1!',
                    $this->escapeHtml($this->_customerSession->getCustomer()->getName()));
            } else {
                $this->_data['welcome'] = $this->_storeConfig->getConfig('design/header/welcome');
            }
        }
        return $this->_data['welcome'];
    }

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getLogoUrl()
    {
        $folderName = \Magento\Backend\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_storeConfig->getConfig('design/header/logo_src');
        $logoUrl = $this->_urlBuilder->getBaseUrl(array('_type' => \Magento\Core\Model\Store::URL_TYPE_MEDIA))
            . $folderName . '/' . $storeLogoPath;
        $absolutePath = $this->_dirs->getDir(\Magento\App\Dir::MEDIA) . DIRECTORY_SEPARATOR
            . $folderName . DIRECTORY_SEPARATOR . $storeLogoPath;

        if (!is_null($storeLogoPath) && $this->_isFile($absolutePath)) {
            $url = $logoUrl;
        } else {
            $url = $this->getViewFileUrl('images/logo.gif');
        }
        return $url;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename)
    {
        $helper = $this->_helperFactory->get('Magento\Core\Helper\File\Storage\Database');

        if ($helper->checkDbUsage() && !is_file($filename)) {
            $helper->saveFileToFilesystem($filename);
        }

        return is_file($filename);
    }
}
