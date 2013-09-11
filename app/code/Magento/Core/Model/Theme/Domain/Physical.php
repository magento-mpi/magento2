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
namespace Magento\Core\Model\Theme\Domain;

class Physical
{
    /**
     * Physical theme model instance
     *
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    /**
     * @var \Magento\Core\Model\ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var \Magento\Core\Model\Theme\CopyService
     */
    protected $_themeCopyService;

    /**
     * @var \Magento\Core\Model\Resource\Theme\Collection
     */
    protected $_themeCollection;

    /**
     * @param \Magento\Core\Model\Theme $theme
     * @param \Magento\Core\Model\ThemeFactory $themeFactory
     * @param \Magento\Core\Model\Theme\CopyService $themeCopyService
     * @param \Magento\Core\Model\Resource\Theme\Collection $themeCollection
     */
    public function __construct(
        \Magento\Core\Model\Theme $theme,
        \Magento\Core\Model\ThemeFactory $themeFactory,
        \Magento\Core\Model\Theme\CopyService $themeCopyService,
        \Magento\Core\Model\Resource\Theme\Collection $themeCollection
    ) {
        $this->_theme = $theme;
        $this->_themeFactory = $themeFactory;
        $this->_themeCopyService = $themeCopyService;
        $this->_themeCollection = $themeCollection;
    }

    /**
     * Create theme customization
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return \Magento\Core\Model\Theme
     */
    public function createVirtualTheme($theme)
    {
        $themeData = $theme->getData();
        $themeData['parent_id'] = $theme->getId();
        $themeData['theme_id'] = null;
        $themeData['theme_path'] = null;
        $themeData['theme_title'] = $this->_getVirtualThemeTitle($theme);
        $themeData['type'] = \Magento\Core\Model\Theme::TYPE_VIRTUAL;

        /** @var $themeCustomization \Magento\Core\Model\Theme */
        $themeCustomization = $this->_themeFactory->create()->setData($themeData);
        $themeCustomization->getThemeImage()->createPreviewImageCopy($theme->getPreviewImage());
        $themeCustomization->save();

        $this->_themeCopyService->copy($theme, $themeCustomization);

        return $themeCustomization;
    }

    /**
     * Get virtual theme title
     *
     * @param \Magento\Core\Model\Theme $theme
     * @return string
     */
    protected function _getVirtualThemeTitle($theme)
    {
        $themeCopyCount = $this->_themeCollection->addAreaFilter(\Magento\Core\Model\App\Area::AREA_FRONTEND)
            ->addTypeFilter(\Magento\Core\Model\Theme::TYPE_VIRTUAL)
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
