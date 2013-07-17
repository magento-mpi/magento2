<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Service model to upload single file in customizations
 */
class Mage_Theme_Model_Theme_SingleFile
{
    /**
     * @var Mage_Core_Model_Theme_Customization_FileInterface
     */
    protected $_fileService;

    /**
     * @param Mage_Core_Model_Theme_Customization_FileInterface $fileService
     */
    public function __construct(Mage_Core_Model_Theme_Customization_FileInterface $fileService)
    {
        $this->_fileService = $fileService;
    }

    /**
     * Creates or updates custom single file which belong to a selected theme
     *
     * @param Mage_Core_Model_Theme $themeModel
     * @param string $fileContent
     */
    public function update(Mage_Core_Model_Theme $themeModel, $fileContent)
    {
        $customFiles = $themeModel->getCustomization()->getFilesByType($this->_fileService->getType());
        $customCss = reset($customFiles);
        if (empty($fileContent) && $customCss) {
            $customCss->delete();
            return;
        }
        if (!$customCss){
            $customCss = $this->_fileService->create();
        }
        $customCss->setData('content', $fileContent);
        $customCss->setTheme($themeModel);
        $customCss->save();
    }
}