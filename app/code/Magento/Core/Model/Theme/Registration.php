<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme registration model class
 */
class Magento_Core_Model_Theme_Registration
{
    /**
     * @var Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Collection of themes in file-system
     *
     * @var Magento_Core_Model_Theme_Collection
     */
    protected $_themeCollection;

    /**
     * Allowed sequence relation by type, array(parent theme, child theme)
     *
     * @var array
     */
    protected $_allowedRelations = array(
        array(Magento_Core_Model_Theme::TYPE_PHYSICAL, Magento_Core_Model_Theme::TYPE_VIRTUAL),
        array(Magento_Core_Model_Theme::TYPE_VIRTUAL, Magento_Core_Model_Theme::TYPE_STAGING)
    );

    /**
     * Forbidden sequence relation by type
     *
     * @var array
     */
    protected $_forbiddenRelations = array(
        array(Magento_Core_Model_Theme::TYPE_VIRTUAL, Magento_Core_Model_Theme::TYPE_VIRTUAL),
        array(Magento_Core_Model_Theme::TYPE_PHYSICAL, Magento_Core_Model_Theme::TYPE_STAGING)
    );

    /**
     * Initialize dependencies
     *
     * @param Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory
     * @param Magento_Core_Model_Theme_Collection $filesystemCollection
     */
    public function __construct(
        Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory,
        Magento_Core_Model_Theme_Collection $filesystemCollection
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_themeCollection = $filesystemCollection;
    }

    /**
     * Theme registration
     *
     * @param string $baseDir
     * @param string $pathPattern
     * @return Magento_Core_Model_Theme
     */
    public function register($baseDir = '', $pathPattern = '')
    {
        if (!empty($baseDir)) {
            $this->_themeCollection->setBaseDir($baseDir);
        }

        if (empty($pathPattern)) {
            $this->_themeCollection->addDefaultPattern('*');
        } else {
            $this->_themeCollection->addTargetPattern($pathPattern);
        }

        foreach ($this->_themeCollection as $theme) {
            $this->_registerThemeRecursively($theme);
        }

        $this->checkPhysicalThemes()->checkAllowedThemeRelations();

        return $this;
    }

    /**
     * Register theme and recursively all its ascendants
     * Second param is optional and is used to prevent circular references in inheritance chain
     *
     * @param Magento_Core_Model_Theme $theme
     * @param array $inheritanceChain
     * @return Magento_Core_Model_Theme_Collection
     * @throws Magento_Core_Exception
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
            throw new Magento_Core_Exception(__('Circular-reference in theme inheritance detected for "%1"', $tempId));
        }
        array_push($inheritanceChain, $tempId);
        $parentTheme = $theme->getParentTheme();
        if ($parentTheme) {
            $this->_registerThemeRecursively($parentTheme, $inheritanceChain);
            $theme->setParentId($parentTheme->getId());
        }

        $this->_savePreviewImage($theme);
        $theme->setType(Magento_Core_Model_Theme::TYPE_PHYSICAL);
        $theme->save();

        return $this;
    }

    /**
     * Save preview image for theme
     *
     * @param Magento_Core_Model_Theme $theme
     * @return $this
     */
    protected function _savePreviewImage(Magento_Core_Model_Theme $theme)
    {
        $themeDirectory = $theme->getCustomization()->getThemeFilesPath();
        if (!$theme->getPreviewImage() || !$themeDirectory) {
            return $this;
        }
        $imagePath = realpath($themeDirectory . DIRECTORY_SEPARATOR . $theme->getPreviewImage());
        if (0 === strpos($imagePath, $themeDirectory)) {
            $theme->getThemeImage()->createPreviewImage($imagePath);
        }
        return $this;
    }

    /**
     * Get theme from DB by full path
     *
     * @param string $fullPath
     * @return Magento_Core_Model_Theme
     */
    public function getThemeFromDb($fullPath)
    {
        return $this->_collectionFactory->create()->getThemeByFullPath($fullPath);
    }

    /**
     * Checks all physical themes that they were not deleted
     *
     * @return Magento_Core_Model_Theme_Registration
     */
    public function checkPhysicalThemes()
    {
        $themes = $this->_collectionFactory->create()->addTypeFilter(Magento_Core_Model_Theme::TYPE_PHYSICAL);
        /** @var $theme Magento_Core_Model_Theme */
        foreach ($themes as $theme) {
            if (!$this->_themeCollection->hasTheme($theme)) {
                $theme->setType(Magento_Core_Model_Theme::TYPE_VIRTUAL)->save();
            }
        }
        return $this;
    }

    /**
     * Check whether all themes have correct parent theme by type
     *
     * @return Magento_Core_Model_Resource_Theme_Collection
     */
    public function checkAllowedThemeRelations()
    {
        foreach ($this->_forbiddenRelations as $typesSequence) {
            list($parentType, $childType) = $typesSequence;
            $collection = $this->_collectionFactory->create();
            $collection->addTypeRelationFilter($parentType, $childType);
            /** @var $theme Magento_Core_Model_Theme */
            foreach ($collection as $theme) {
                $parentId = $this->_getResetParentId($theme);
                if ($theme->getParentId() != $parentId) {
                    $theme->setParentId($parentId)->save();
                }
            }
        }
        return $this;
    }

    /**
     * Reset parent themes by type
     *
     * @param Magento_Core_Model_Theme $theme
     * @return int|null
     */
    protected function _getResetParentId(Magento_Core_Model_Theme $theme)
    {
        $parentTheme = $theme->getParentTheme();
        while ($parentTheme) {
            foreach ($this->_allowedRelations as $typesSequence) {
                list($parentType, $childType) = $typesSequence;
                if ($theme->getType() == $childType && $parentTheme->getType() == $parentType) {
                    return $parentTheme->getId();
                }
            }
            $parentTheme = $parentTheme->getParentTheme();
        }
        return null;
    }
}
