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
class Magento_Theme_Model_Theme_SingleFile
{
    /**
     * @var Magento_Core_Model_Theme_Customization_FileInterface
     */
    protected $_fileService;

    /**
     * @param Magento_Core_Model_Theme_Customization_FileInterface $fileService
     */
    public function __construct(Magento_Core_Model_Theme_Customization_FileInterface $fileService)
    {
        $this->_fileService = $fileService;
    }

    /**
     * Creates or updates custom single file which belong to a selected theme
     *
     * @param Magento_Core_Model_Theme $themeModel
     * @param string $fileContent
     * @return Magento_Core_Model_Theme_FileInterface
     */
    public function update(Magento_Core_Model_Theme $themeModel, $fileContent)
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
