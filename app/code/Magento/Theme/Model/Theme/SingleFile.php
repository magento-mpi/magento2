<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model to upload single file in customizations
 */
namespace Magento\Theme\Model\Theme;

class SingleFile
{
    /**
     * @var \Magento\View\Design\Theme\Customization\FileInterface
     */
    protected $_fileService;

    /**
     * @param \Magento\View\Design\Theme\Customization\FileInterface $fileService
     */
    public function __construct(\Magento\View\Design\Theme\Customization\FileInterface $fileService)
    {
        $this->_fileService = $fileService;
    }

    /**
     * Creates or updates custom single file which belong to a selected theme
     *
     * @param \Magento\View\Design\ThemeInterface $themeModel
     * @param string $fileContent
     * @return \Magento\View\Design\Theme\FileInterface
     */
    public function update(\Magento\View\Design\ThemeInterface $themeModel, $fileContent)
    {
        $customFiles = $themeModel->getCustomization()->getFilesByType($this->_fileService->getType());
        $customCss = reset($customFiles);
        if (empty($fileContent) && $customCss) {
            $customCss->delete();
            return $customCss;
        }
        if (!$customCss) {
            $customCss = $this->_fileService->create();
        }
        $customCss->setData('content', $fileContent);
        $customCss->setTheme($themeModel);
        $customCss->save();
        return $customCss;
    }
}
