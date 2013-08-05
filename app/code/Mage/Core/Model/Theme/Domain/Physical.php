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
 * Physical theme model class
 */
class Mage_Core_Model_Theme_Domain_Physical
{
    /**
     * Physical theme model instance
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * @var Mage_Core_Model_Resource_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_ThemeFactory $themeFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_CopyService $themeCopyService
     * @param Mage_Core_Model_Resource_Theme_Collection $themeCollection
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_ThemeFactory $themeFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Theme_CopyService $themeCopyService,
        Mage_Core_Model_Resource_Theme_Collection $themeCollection
    ) {
        $this->_theme = $theme;
        $this->_themeFactory = $themeFactory;
        $this->_helper = $helper;
        $this->_themeCopyService = $themeCopyService;
        $this->_themeCollection = $themeCollection;
    }

    /**
     * Create theme customization
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme
     */
    public function createVirtualTheme($theme)
    {
        $themeData = $theme->getData();
        $themeData['parent_id'] = $theme->getId();
        $themeData['theme_id'] = null;
        $themeData['theme_path'] = null;
        $themeData['theme_title'] = $this->_getVirtualThemeTitle($theme);
        $themeData['type'] = Mage_Core_Model_Theme::TYPE_VIRTUAL;

        /** @var $themeCustomization Mage_Core_Model_Theme */
        $themeCustomization = $this->_themeFactory->create()->setData($themeData);
        $themeCustomization->getThemeImage()->createPreviewImageCopy($theme->getPreviewImage());
        $themeCustomization->save();

        $this->_themeCopyService->copy($theme, $themeCustomization);

        return $themeCustomization;
    }

    /**
     * Get virtual theme title
     *
     * @param Mage_Core_Model_Theme $theme
     * @return string
     */
    protected function _getVirtualThemeTitle($theme)
    {
        $themeCopyCount = $this->_themeCollection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL)
            ->addFilter('parent_id', $theme->getId())
            ->count();

        $title = sprintf(
            "%s - %s #%s",
            $theme->getThemeTitle(),
            __('Copy'),
            ($themeCopyCount + 1)
        );
        return $title;
    }
}
