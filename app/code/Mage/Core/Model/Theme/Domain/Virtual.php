<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Virtual theme domain model
 */
class Mage_Core_Model_Theme_Domain_Virtual
{
    /**
     * Virtual theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_ThemeFactory $themeFactory
     */
    protected $_themeFactory;

    /**
     * Staging theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_stagingTheme;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * Theme customization config
     *
     * @var Mage_Theme_Model_Config_Customization
     */
    protected $_customizationConfig;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_ThemeFactory $themeFactory
     * @param Mage_Core_Model_Theme_CopyService $themeCopyService
     * @param Mage_Theme_Model_Config_Customization $customizationConfig
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_ThemeFactory $themeFactory,
        Mage_Core_Model_Theme_CopyService $themeCopyService,
        Mage_Theme_Model_Config_Customization $customizationConfig
    ) {
        $this->_theme = $theme;
        $this->_themeFactory = $themeFactory;
        $this->_themeCopyService = $themeCopyService;
        $this->_customizationConfig = $customizationConfig;
    }

    /**
     * Get 'staging' theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getStagingTheme()
    {
        if (!$this->_stagingTheme) {
            $this->_stagingTheme = $this->_theme->getStagingVersion();
            if (!$this->_stagingTheme) {
                $this->_stagingTheme = $this->_createStagingTheme();
                $this->_themeCopyService->copy($this->_theme, $this->_stagingTheme);
            }
        }
        return $this->_stagingTheme;
    }

    /**
     * Get 'physical' theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getPhysicalTheme()
    {
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->_theme->getParentTheme();
        while ($parentTheme && !$parentTheme->isPhysical()) {
            $parentTheme = $parentTheme->getParentTheme();
        }

        if (!$parentTheme || !$parentTheme->getId()) {
            return null;
        }

        return $parentTheme;
    }

    /**
     * Check if theme is assigned to ANY store
     *
     * @return bool
     */
    public function isAssigned()
    {
        return $this->_customizationConfig->isThemeAssignedToStore($this->_theme);
    }

    /**
     * Create 'staging' theme associated with current 'virtual' theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _createStagingTheme()
    {
        $stagingTheme = $this->_themeFactory->create();
        $stagingTheme->setData(array(
            'parent_id'            => $this->_theme->getId(),
            'theme_path'           => null,
            'theme_version'        => $this->_theme->getThemeVersion(),
            'theme_title'          => sprintf('%s - Staging', $this->_theme->getThemeTitle()),
            'preview_image'        => $this->_theme->getPreviewImage(),
            'is_featured'          => $this->_theme->getIsFeatured(),
            'area'                 => $this->_theme->getArea(),
            'type'                 => Mage_Core_Model_Theme::TYPE_STAGING
        ));
        $stagingTheme->save();
        return $stagingTheme;
    }
}
