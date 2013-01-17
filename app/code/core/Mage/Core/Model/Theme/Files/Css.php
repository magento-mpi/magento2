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
class Mage_Core_Model_Theme_Files_Css extends Mage_Core_Model_Theme_Files_Abstract
{
    /**
     * Css file name
     */
    const FILE_NAME = 'custom.css';

    /**
     * Return file type
     *
     * @return string
     */
    protected function _getFileType()
    {
        return Mage_Core_Model_Theme_Files::TYPE_CSS;
    }

    /**
     * Save data
     *
     * @param $theme Mage_Core_Model_Theme
     * @return Mage_Core_Model_Theme_Files_Css
     */
    protected function _save($theme)
    {
        /** @var $cssModel Mage_Core_Model_Theme_Files */
        $cssFile = $this->getFileByTheme($theme);
        $cssFile->addData(array(
            'theme_id'  => $theme->getId(),
            'file_name' => self::FILE_NAME,
            'file_type' => $this->_getFileType(),
            'content'   => $this->_dataForSave
        ))->save();

        return $this;
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
        $cssFile = $this->getCollectionByTheme($theme)->getFirstItem();
        return $cssFile;
    }
}
