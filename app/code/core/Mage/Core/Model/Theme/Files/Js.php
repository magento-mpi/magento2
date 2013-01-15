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
}
