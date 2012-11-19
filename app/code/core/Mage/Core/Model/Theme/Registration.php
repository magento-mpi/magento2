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
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Init theme model
     */
    public function __construct(Mage_Core_Model_Theme $model)
    {
        $this->setThemeModel($model);
    }

    /**
     * Get theme model
     *
     * @return Mage_Core_Model_Theme
     */
    public function getThemeModel()
    {
        return $this->_theme;
    }

    /**
     * Set theme model
     *
     * @param Mage_Core_Model_Theme $theme
     * @return Mage_Core_Model_Theme_Registration
     */
    public function setThemeModel($theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Theme registration
     *
     * @param string $pathPattern
     * @return Mage_Core_Model_Theme
     */
    public function register($pathPattern = '')
    {
        $this->_collection = $this->getThemeModel()->getCollectionFromFilesystem();

        if ($pathPattern) {
            $this->_collection->addTargetPattern($pathPattern);
        } else {
            $this->_collection->addDefaultPattern('*');
        }

        foreach ($this->_collection as $theme) {
            $this->_registerThemeRecursively($theme);
        }

        /** @var $dbCollection Mage_Core_Model_Resource_Theme_Collection */
        $dbCollection = $this->getThemeModel()->getResourceCollection();
        $dbCollection->checkParentInThemes();

        return $this;
    }

    /**
     * Register theme and recursively all its ascendants
     * Second param is optional and is used to prevent circular references in inheritance chain
     *
     * @param Mage_Core_Model_Theme $theme
     * @param array $inheritanceChain
     * @return Mage_Core_Model_Theme_Collection
     * @throws Mage_Core_Exception
     */
    protected function _registerThemeRecursively($theme, $inheritanceChain = array())
    {
        if ($theme->getId()) {
            return $this;
        }
        $themeModel = $this->getThemeFromDb($theme->getFullPath());
        if ($themeModel->getId()) {
            $theme = $themeModel;
            return $this;
        }

        $tempId = $theme->getFullPath();
        if (in_array($tempId, $inheritanceChain)) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')
                    ->__('Circular-reference in theme inheritance detected for "%s"', $tempId));
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
     * Get theme from DB by full path
     *
     * @param string $fullPath
     * @return Mage_Core_Model_Theme
     */
    public function getThemeFromDb($fullPath)
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->getThemeModel()->getCollection();
        return $collection->getThemeByFullPath($fullPath);
    }
}
