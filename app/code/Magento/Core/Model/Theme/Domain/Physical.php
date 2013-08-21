<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Physical theme model class
 */
class Magento_Core_Model_Theme_Domain_Physical
{
    /**
     * Physical theme model instance
     *
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var Magento_Core_Model_Theme_CopyService
     */
    protected $_themeCopyService;

    /**
     * @var Magento_Core_Model_Resource_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * @param Magento_Core_Model_Theme $theme
     * @param Magento_Core_Model_ThemeFactory $themeFactory
     * @param Magento_Core_Model_Theme_CopyService $themeCopyService
     * @param Magento_Core_Model_Resource_Theme_Collection $themeCollection
     */
    public function __construct(
        Magento_Core_Model_Theme $theme,
        Magento_Core_Model_ThemeFactory $themeFactory,
        Magento_Core_Model_Theme_CopyService $themeCopyService,
        Magento_Core_Model_Resource_Theme_Collection $themeCollection
    ) {
        $this->_theme = $theme;
        $this->_themeFactory = $themeFactory;
        $this->_themeCopyService = $themeCopyService;
        $this->_themeCollection = $themeCollection;
    }

    /**
     * Create theme customization
     *
     * @param Magento_Core_Model_Theme $theme
     * @return Magento_Core_Model_Theme
     */
    public function createVirtualTheme($theme)
    {
        $themeData = $theme->getData();
        $themeData['parent_id'] = $theme->getId();
        $themeData['theme_id'] = null;
        $themeData['theme_path'] = null;
        $themeData['theme_title'] = $this->_getVirtualThemeTitle($theme);
        $themeData['type'] = Magento_Core_Model_Theme::TYPE_VIRTUAL;

        /** @var $themeCustomization Magento_Core_Model_Theme */
        $themeCustomization = $this->_themeFactory->create()->setData($themeData);
        $themeCustomization->getThemeImage()->createPreviewImageCopy($theme->getPreviewImage());
        $themeCustomization->save();

        $this->_themeCopyService->copy($theme, $themeCustomization);

        return $themeCustomization;
    }

    /**
     * Get virtual theme title
     *
     * @param Magento_Core_Model_Theme $theme
     * @return string
     */
    protected function _getVirtualThemeTitle($theme)
    {
        $themeCopyCount = $this->_themeCollection->addAreaFilter(Magento_Core_Model_App_Area::AREA_FRONTEND)
            ->addTypeFilter(Magento_Core_Model_Theme::TYPE_VIRTUAL)
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
