<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
    /**
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_themeService;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Core_Model_Theme_Service $themeService
     * @param Magento_ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Launcher_Model_LinkTracker $linkTracker,
        Mage_Core_Model_Theme_Service $themeService,
        Magento_ObjectManager $objectManager,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_themeService = $themeService;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Store Design');
    }

    /**
     * Get Themes
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getThemes()
    {
        return $this->_themeService->getPhysicalThemes();
    }

    /**
     * Retrieve current theme ID
     *
     * @return int|null
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue('design/theme/theme_id', $this->_getCurrentStoreId());
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getCurrentTheme()
    {
        return $this->_themeService->getThemeById($this->getCurrentThemeId());
    }

    /**
     * Retrieve array of themes blocks
     *
     * @return array|null
     */
    public function getThemesBlocks()
    {
        $themesBlocks = array();
        /** @var $block Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme */
        $block = $this->getChildBlock('theme-preview');
        foreach ($this->getThemes() as $theme) {
            $themeBlock = clone $block;
            $themeBlock->setTheme($theme);
            $themesBlocks[] = $themeBlock;
        }

        return $themesBlocks;
    }

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    public function getLogoUrl()
    {
        /** @var Mage_Core_Helper_File_Storage_Database $helper */
        $helper = $this->_helperFactory->get('Mage_Core_Helper_File_Storage_Database');

        $folderName = Mage_Backend_Model_Config_Backend_Image_Logo::UPLOAD_DIR;
        $storeLogoPath = $this->getConfigValue('design/header/logo_src', $this->_getCurrentStoreId());
        $logoUrl = $this->_urlBuilder->getBaseUrl(array('_type' => Mage_Core_Model_Store::URL_TYPE_MEDIA))
            . $folderName . '/' . $storeLogoPath;
        $absolutePath = $this->_dirs->getDir(Mage_Core_Model_Dir::MEDIA) . DIRECTORY_SEPARATOR
            . $folderName . DIRECTORY_SEPARATOR . $storeLogoPath;

        if (!is_null($storeLogoPath) && $this->_isFile($absolutePath)) {
            return $logoUrl;
        }

        return '';
    }

    /**
     * If DB file storage is on - find there, otherwise - just check file exists
     *
     * @param string $filename
     * @return bool
     */
    protected function _isFile($filename)
    {
        /** @var Mage_Core_Helper_File_Storage_Database $helper */
        $helper = $this->_helperFactory->get('Mage_Core_Helper_File_Storage_Database');
        /** @var $fileSystem Magento_Filesystem */
        $fileSystem = $this->_objectManager->get('Magento_Filesystem');

        if ($helper->checkDbUsage() && !$fileSystem->isFile($filename)) {
            $helper->saveFileToFilesystem($filename);
        }

        return $fileSystem->isFile($filename);
    }

    /**
     * Retrieve current Store ID
     *
     * @return int|null
     */
    protected function _getCurrentStoreId()
    {
        $store = $this->_helperFactory->get('Mage_Launcher_Helper_Data')->getCurrentStoreView();
        $storeId = $store ? $store->getId() : null;
        return $storeId;
    }

}
