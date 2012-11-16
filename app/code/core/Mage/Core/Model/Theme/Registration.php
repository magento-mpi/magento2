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
 * Theme registration model class
 *
 */
class Mage_Core_Model_Theme_Registration
{
    /**
     * Collection of themes in file-system
     *
     * @var Mage_Core_Model_Theme_Collection
     */
    protected $_collection;

    /**
     * Theme registration
     *
     * @param string $pathPattern
     * @return Mage_Core_Model_Theme
     */
    public function register($pathPattern = '')
    {
        $this->_collection = Mage::getSingleton('Mage_Core_Model_Theme_Collection');

        if ($pathPattern) {
            $this->_collection->addTargetPattern($pathPattern);
        } else {
            $this->_collection->addDefaultPattern('*');
        }

        foreach ($this->_collection as $theme) {
            $this->_registerThemeRecursively($theme);
        }

        /** @var $dbCollection Mage_Core_Model_Resource_Theme_Collection */
        $dbCollection = Mage::getModel('Mage_Core_Model_Theme')->getResourceCollection();
        $dbCollection->checkParentInThemes();

        return $this;
    }

    /**
     * Register theme and recursively all its ascendants
     * Second param is optional and is used to prevent circular references in inheritance chain
     *
     * @throws Mage_Core_Exception
     * @param Mage_Core_Model_Theme $theme
     * @param array $inheritanceChain
     * @return Mage_Core_Model_Theme_Collection
     */
    protected function _registerThemeRecursively($theme, $inheritanceChain = array())
    {
        if ($theme->getId()) {
            return $this;
        }
        $themeModel = $this->getThemeFromDb($theme->getArea(), $theme->getThemePath());
        if ($themeModel->getId()) {
            $theme = $themeModel;
            return $this;
        }

        $tempId = $theme->getTempId();
        if (in_array($tempId, $inheritanceChain)) {
            Mage::throwException(
                //TODO fix line length
                Mage::helper('Mage_Core_Helper_Data')->__('Circular-reference in theme inheritance detected for "%s"', $tempId)
            );
        }
        array_push($inheritanceChain, $tempId);
        $parentTheme = $theme->getParentTheme();
        if ($parentTheme) {
            $this->_registerThemeRecursively($parentTheme, $inheritanceChain);
            $theme->setParentId($parentTheme->getId());

        }

        $theme->savePreviewImage()->save();
        return $this;
    }

    /**
     * Find theme in file-system by theme path
     *
     * @throws Mage_Core_Exception
     * @param string $area
     * @param string $themePath
     * @return Mage_Core_Model_Theme|null
     */
    protected function _findTheme($area, $themePath)
    {
        $theme = null;
        /** @var $item Mage_Core_Model_Theme */
        foreach ($this->_collection->getItemsByColumnValue('theme_path', $themePath) as $item) {
            if ($item->getArea() == $area) {
                $theme = $item;
                break;
            }
        }
        if ($theme === null) {
            Mage::throwException(
                Mage::helper('Mage_Core_Helper_Data')->__('Invalid parent theme "%s"', $themePath)
            );
        }
        return $theme;
    }

    /**
     * Get theme from DB
     *
     * @param string $area
     * @param string $themePath
     * @return Mage_Core_Model_Theme
     */
    public function getThemeFromDb($area, $themePath)
    {
        return Mage::getModel('Mage_Core_Model_Theme')->loadByTempId($area . '/' . $themePath);
    }
}
