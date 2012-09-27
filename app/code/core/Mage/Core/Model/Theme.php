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
 * Theme model class
 *
 * @method Mage_Core_Model_Theme save()
 * @method string getThemeCode()
 * @method string getParentTheme()
 * @method string getThemePath()
 * @method Mage_Core_Model_Theme setParentTheme(string $parentTheme)
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
{
    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Themes collection loaded from file system configurations
     *
     * @return Mage_Core_Model_Theme_Collection
     */
    public function getCollectionFromFilesystem()
    {
        return Mage::getModel('Mage_Core_Model_Theme_Collection');
    }

    /**
     * Loads data that contains in configuration file (theme.xml)
     *
     * @param string $configPath
     * @return Mage_Core_Model_Theme
     */
    public function loadFromConfiguration($configPath)
    {
        $themeConfig = $this->_getConfigModel(array($configPath));

        $packageCodes = $themeConfig->getPackageCodes();
        $packageCode = reset($packageCodes);
        $themeCodes = $themeConfig->getPackageThemeCodes($packageCode);
        $themeCode = reset($themeCodes);

        $themeVersions = $themeConfig->getCompatibleVersions($packageCode, $themeCode);
        $media = $themeConfig->getMedia($packageCode, $themeCode);
        $this->setData(array(
            'theme_code'           => $themeCode,
            'theme_title'          => $themeConfig->getThemeTitle($packageCode, $themeCode),
            'theme_version'        => $themeConfig->getThemeVersion($packageCode, $themeCode),
            'parent_theme'         => $themeConfig->getParentTheme($packageCode, $themeCode),
            'featured'             => $themeConfig->getFeatured($packageCode, $themeCode),
            'magento_version_from' => $themeVersions['from'],
            'magento_version_to'   => $themeVersions['to'],
            'theme_path'           => $packageCode . '/' . $themeCode,
            'preview_image'        => $media['preview_image'],
            'theme_directory'      => $this->_getThemeDir($configPath),
        ));
        return $this;
    }

    /**
     * Get theme directory
     *
     * @param string $configPath
     * @return string
     */
    protected function _getThemeDir($configPath)
    {
        /**
         * Replace last 9 symbols(theme.xml) from config path.
         * As result we retrieve theme base directory.
         */
        return substr($configPath, 0, -9);
    }

    /**
     * Return configuration model for themes
     *
     * @param array $configPaths
     * @return Magento_Config_Theme
     */
    protected function _getConfigModel(array $configPaths)
    {
        return new Magento_Config_Theme($configPaths);
    }

    /**
     * Validate theme data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Theme
     */
    protected function _validate()
    {
        /** @var $validator Mage_Core_Model_Theme_Validator */
        $validator = Mage::getModel('Mage_Core_Model_Theme_Validator');
        if (!$validator->validate($this)) {
            $messages = $validator->getErrorMessages();
            Mage::throwException(implode(PHP_EOL, reset($messages)));
        }
        return $this;
    }

    /**
     * Check theme is existing in filesystem
     *
     * @return bool
     */
    public function isDeletable()
    {
        $collection = $this->getCollectionFromFilesystem()->addDefaultPattern()->getItems();
        return !($this->getThemePath() && isset($collection[$this->getThemePath()]));
    }

    /**
     * Update all child themes relations
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _updateChildRelations()
    {
        $parentThemeId = $this->getParentId();
        /** @var $childThemes Mage_Core_Model_Resource_Theme_Collection */
        $childThemes = $this->getCollection();
        $childThemes->addFieldToFilter('parent_id', array('eq' => $this->getId()))->load();

        /** @var $theme Mage_Core_Model_Theme */
        foreach ($childThemes->getItems() as $theme) {
            $theme->setParentId($parentThemeId)->save();
        }

        return $this;
    }

    /**
     * Before theme save
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Theme
     */
    protected function _beforeSave()
    {
        $this->_validate()->_savePreviewImage();
        return parent::_beforeSave();
    }

    /**
     * Processing theme before deleting data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Theme
     */
    protected function _beforeDelete()
    {
        if (!$this->isDeletable()) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Current theme isn\'t deletable.'));
        }
        return parent::_beforeDelete();
    }

    /**
     * Update all relations after deleting theme
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _afterDelete()
    {
        $this->_updateChildRelations();
        return parent::_afterDelete();
    }

    /**
     * Save preview image
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _savePreviewImage()
    {
        if (!$this->getPreviewImage()) {
            return $this;
        }
        $themeDirectory = $this->getThemeDirectory();
        $currentWorkingDir = getcwd();

        @chdir($themeDirectory);

        $imagePath = realpath($this->getPreviewImage());

        if ($imagePath) {
            $this->createPreviewImage($themeDirectory . $imagePath);
        }

        @chdir($currentWorkingDir);

        return $this;
    }
}
