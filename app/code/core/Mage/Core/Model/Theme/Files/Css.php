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
 * Theme css file model class
 */
class Mage_Core_Model_Theme_Files_Css
{
    /**
     * Css file name
     */
    const FILE_NAME = 'custom.css';

    /**
     * @var Mage_Core_Model_Theme_Files
     */
    protected $_themeFiles;

    /**
     * @param Mage_Core_Model_Theme_Files $themeFiles
     */
    public function __construct(Mage_Core_Model_Theme_Files $themeFiles)
    {
        $this->_themeFiles = $themeFiles;
    }

    /**
     * Save data from form
     *
     * @param $theme Mage_Core_Model_Theme
     * @param string $themeCssData
     * @return Mage_Core_Model_Theme_Files
     */
    public function saveFormData($theme, $themeCssData)
    {
        /** @var $cssModel Mage_Core_Model_Theme_Files */
        $cssFile = $this->getFileByTheme($theme);
        $cssFile->addData(array(
            'theme_id'  => $theme->getId(),
            'file_name' => self::FILE_NAME,
            'file_type' => Mage_Core_Model_Theme_Files::TYPE_CSS,
            'content'   => $themeCssData
        ))->save();
        return $cssFile;
    }

    /**
     * Return theme css file by theme
     *
     * @param $theme Mage_Core_Model_Theme
     * @return Mage_Core_Model_Theme_Files
     */
    public function getFileByTheme($theme)
    {
        /** @var $cssModel Mage_Core_Model_Theme_Files */
        $cssFile = $this->_themeFiles->getCollection()
            ->addFilter('theme_id', $theme->getId())
            ->addFilter('file_type', Mage_Core_Model_Theme_Files::TYPE_CSS)
            ->getFirstItem();

        return $cssFile;
    }
}
