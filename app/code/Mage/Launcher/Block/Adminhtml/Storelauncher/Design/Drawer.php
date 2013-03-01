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
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Core_Model_Theme_Service $themeService
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Launcher_Model_LinkTracker $linkTracker,
        Mage_Core_Model_Theme_Service $themeService,
        array $data = array()
    ) {
        parent::__construct($context, $linkTracker, $data);
        $this->_themeService = $themeService;
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
        return $this->_themeService->getAllThemes();
    }

    /**
     * Retrieve current theme ID
     *
     * @return int|null
     */
    public function getCurrentThemeId()
    {
        $store = $this->_helperFactory->get('Mage_Launcher_Helper_Data')->getCurrentStoreView();
        $storeId = $store ? $store->getId() : null;
        return $this->getConfigValue('design/theme/theme_id', $storeId);
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
        foreach($this->getThemes() as $theme) {
            $themeBlock = clone $block;
            $themeBlock->setTheme($theme);
            $themesBlocks[] = $themeBlock;
        }

        return $themesBlocks;
    }

}
