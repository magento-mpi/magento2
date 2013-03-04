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
     * @var Mage_Core_Model_Resource_Theme_Collection
     */
    protected $_resourceCollection;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * Types which will be checked for relations
     *
     * @var array
     */
    protected $_allowedTypes = array(
        Mage_Core_Model_Theme::TYPE_PHYSICAL,
        Mage_Core_Model_Theme::TYPE_VIRTUAL
    );

    /**
     * Init theme model
     *
     * @param Mage_Core_Model_Theme $theme
     */
    public function __construct(Mage_Core_Model_Theme $theme)
    {
        $this->_theme = $theme;
        $this->_collection = $theme->getCollectionFromFilesystem();
        $this->_resourceCollection = $theme->getCollection();
    }

    /**
     * Theme registration
     *
     * @param string $baseDir
     * @param string $pathPattern
     * @return Mage_Core_Model_Theme
     */
    public function register($baseDir = '', $pathPattern = '')
    {
        $this->_collection->setBaseDir($baseDir);
        if (empty($pathPattern)) {
            $this->_collection->addDefaultPattern('*');
        } else {
            $this->_collection->addTargetPattern($pathPattern);
        }

        foreach ($this->_collection as $theme) {
            $this->_registerThemeRecursively($theme);
        }

        $this->checkParentInThemes();

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
    protected function _registerThemeRecursively(&$theme, $inheritanceChain = array())
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

        $theme->getThemeImage()->savePreviewImage();
        $theme->setType(Mage_Core_Model_Theme::TYPE_PHYSICAL);
        $theme->save();

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
        return $this->_theme->getCollection()->getThemeByFullPath($fullPath);
    }

    /**
     * Check whether all themes have non virtual parent theme
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function checkParentInThemes()
    {
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($this->_resourceCollection->getItems() as $theme) {
            if ($theme->getParentId() && in_array($theme->getType(), $this->_allowedTypes)) {
                $newParentId = $this->_getParentThemeRecursively($theme->getParentId());
                if ($newParentId != $theme->getParentId()) {
                    $theme->setParentId($newParentId);
                    $theme->save();
                }
            }
        }
        return $this;
    }

    /**
     * Get parent non virtual theme recursively
     *
     * @param int $parentId
     * @return int|null
     */
    protected function _getParentThemeRecursively($parentId)
    {
        /** @var $parentTheme Mage_Core_Model_Theme */
        $parentTheme = $this->_resourceCollection->getItemById($parentId);
        if (!$parentTheme->getId() || ($parentTheme->isPresentInFilesystem() && !$parentTheme->getParentId())) {
            $parentId = null;
        } elseif ($parentTheme->isPresentInFilesystem()) {
            $parentId = $this->_getParentThemeRecursively($parentTheme->getParentId());
        }
        return $parentId;
    }
}
