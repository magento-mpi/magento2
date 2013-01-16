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
 * Theme js file model class
 */
class Mage_Core_Model_Theme_Files_Js
{
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
     * Return theme js files by theme
     *
     * @param $theme Mage_Core_Model_Theme
     * @return Mage_Core_Model_Resource_Theme_Files_Collection
     */
    public function getFilesByTheme($theme)
    {
        /** @var $jsFiles Mage_Core_Model_Resource_Theme_Files_Collection */
        $jsFiles = $this->_themeFiles->getCollection()
            ->setOrder('`order`', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFilter('theme_id', $theme->getId())
            ->addFilter('file_type', Mage_Core_Model_Theme_Files::TYPE_JS);
        return $jsFiles;
    }

    /**
     * Remove temporary files
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme_Files_Js
     */
    public function removeTemporaryFiles($theme)
    {
        /** @var $jsFiles Mage_Core_Model_Resource_Theme_Files_Collection */
        $jsFiles = $this->_themeFiles->getCollection()
            ->addFilter('is_temporary', true)
            ->addFilter('theme_id', $theme->getId())
            ->addFilter('file_type', Mage_Core_Model_Theme_Files::TYPE_JS);

        /** @var $file Mage_Core_Model_Theme_Files */
        foreach ($jsFiles as $file) {
            $file->delete();
        }

        return $this;
    }

    /**
     * Save form data
     *
     * @param Mage_Core_Model_Theme $theme
     * @param array $themeJsFiles
     * @return Mage_Core_Model_Theme_Files_Js
     */
    public function saveFormData($theme, array $themeJsFiles)
    {
        $themeFile = $this->_themeFiles;
        foreach ($themeJsFiles as $fileId) {
            $themeFile->load($fileId);
            if ($themeFile->getId()) {
                $themeFile->setIsTemporary(false)->save();
            }
        }
        return $this;
    }

    /**
     * Save js file
     *
     * @param Mage_Core_Model_Theme $theme
     * @param array $file
     * @param bool $temporary
     * @return Mage_Core_Model_Theme_Files
     */
    public function saveJsFile($theme, $file, $temporary = true)
    {
        $this->_themeFiles->addData(array(
            'theme_id'  => $theme->getId(),
            'file_name' => $this->_prepareFileName($theme, $file['name']),
            'file_type' => Mage_Core_Model_Theme_Files::TYPE_JS,
            'content'   => $file['content'],
            'is_temporary' => $temporary
        ))->save();
    }

    /**
     * Prepare file name
     *
     * @param Mage_Core_Model_Theme $theme
     * @param string $newFileName
     * @return string
     */
    protected function _prepareFileName($theme, $newFileName)
    {
        $fileInfo = pathinfo($newFileName);
        $index = 1;
        while ($this->_getThemeFileByName($theme, $newFileName)->getId()) {
            $newFileName = $fileInfo['filename'] . '_' . $index . '.' . $fileInfo['extension'];
            $index++;
        }

        return $newFileName;
    }

    /**
     * Get theme js files by name
     *
     * @param Mage_Core_Model_Theme $theme
     * @param string $fileName
     * @return Mage_Core_Model_Resource_Theme_Files_Collection
     */
    protected function _getThemeFileByName($theme, $fileName)
    {
        /** @var $jsFile Mage_Core_Model_Resource_Theme_Files_Collection */
        $jsFile = $this->_themeFiles->getCollection()
            ->setOrder('`order`', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFilter('theme_id', $theme->getId())
            ->addFieldToFilter('file_name', array('like' => $fileName))
            ->getFirstItem();

        return $jsFile;
    }
}
