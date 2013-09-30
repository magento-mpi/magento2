<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Html page block
 *
 * @category   Magento
 * @package    Magento_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Page_Block_Html_Header extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_App_State $appState,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_appState = $appState;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        $this->setTemplate('html/header.phtml');
    }

    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }

    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
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

    public function getLogoAlt()
    {
        if (empty($this->_data['logo_alt'])) {
            $this->_data['logo_alt'] = $this->_storeConfig->getConfig('design/header/logo_alt');
        }
        return $this->_data['logo_alt'];
    }

    public function getWelcome()
    {
        if (empty($this->_data['welcome'])) {
            if ($this->_appState->isInstalled() && $this->_customerSession->isLoggedIn()) {
                $this->_data['welcome'] = __('Welcome, %1!', $this->escapeHtml($this->_customerSession->getCustomer()->getName()));
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
        $folderName = Magento_Backend_Model_Config_Backend_Image_Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_storeConfig->getConfig('design/header/logo_src');
        $logoUrl = $this->_urlBuilder->getBaseUrl(array('_type' => Magento_Core_Model_Store::URL_TYPE_MEDIA))
            . $folderName . '/' . $storeLogoPath;
        $absolutePath = $this->_dirs->getDir(Magento_Core_Model_Dir::MEDIA) . DIRECTORY_SEPARATOR
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
        $helper = $this->_helperFactory->get('Magento_Core_Helper_File_Storage_Database');

        if ($helper->checkDbUsage() && !is_file($filename)) {
            $helper->saveFileToFilesystem($filename);
        }

        return is_file($filename);
    }
}
