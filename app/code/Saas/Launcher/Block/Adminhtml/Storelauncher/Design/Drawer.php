<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Drawer Block
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer extends Saas_Launcher_Block_Adminhtml_Drawer
{
    /**
     * @var Magento_Core_Model_ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Saas_Launcher_Model_LinkTracker $linkTracker
     * @param Magento_Core_Model_ThemeFactory $themeFactory
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Saas_Launcher_Model_LinkTracker $linkTracker,
        Magento_Core_Model_ThemeFactory $themeFactory,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_themeFactory = $themeFactory;
        $this->_objectManager = $objectManager;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Saas_Launcher_Helper_Data')->__('Store Design');
    }

    /**
     * Get Themes
     *
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function getThemes()
    {
        /** @var Magento_Core_Model_Resource_Theme_Collection $themeCollection */
        $themeCollection = $this->_collectionFactory->create();
        return $themeCollection->filterPhysicalThemes();
    }

    /**
     * Retrieve current theme ID
     *
     * @return int|null
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue(Magento_Core_Model_View_Design::XML_PATH_THEME_ID,
            $this->_getCurrentStoreId());
    }

    /**
     * Get current theme
     *
     * @return Magento_Core_Model_Theme
     */
    public function getCurrentTheme()
    {
        return $this->_themeFactory->create()->load($this->getCurrentThemeId());
    }

    /**
     * Retrieve array of themes blocks
     *
     * @return array|null
     */
    public function getThemesBlocks()
    {
        $themesBlocks = array();
        /** @var $block Saas_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme */
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
        $folderName = Magento_Backend_Model_Config_Backend_Image_Logo::UPLOAD_DIR;
        $storeLogoPath = $this->getConfigValue(Saas_Launcher_Model_Storelauncher_Design_SaveHandler::XML_PATH_LOGO,
            $this->_getCurrentStoreId());
        $logoUrl = $this->_urlBuilder->getBaseUrl(array('_type' => Magento_Core_Model_Store::URL_TYPE_MEDIA))
            . $folderName . '/' . $storeLogoPath;
        $absolutePath = $this->_dirs->getDir(Magento_Core_Model_Dir::MEDIA) . DIRECTORY_SEPARATOR
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
        /** @var Magento_Core_Helper_File_Storage_Database $helper */
        $helper = $this->_helperFactory->get('Magento_Core_Helper_File_Storage_Database');
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
        $store = $this->_helperFactory->get('Saas_Launcher_Helper_Data')->getCurrentStoreView();
        $storeId = $store ? $store->getId() : null;
        return $storeId;
    }

}
