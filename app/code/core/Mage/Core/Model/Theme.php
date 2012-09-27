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
 * @method setPreviewImage(string $previewImage)
 * @method string getPreviewImage()
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
{
    /**
     * Theme directory
     */
    const THEME_DIR = 'theme';

    /**
     * Preview image directory
     */
    const IMAGE_DIR_PREVIEW = 'preview';

    /**
     * Origin image directory
     */
    const IMAGE_DIR_ORIGIN = 'origin';

    /**
     * Preview image width
     */
    const PREVIEW_IMAGE_WIDTH = 200;

    /**
     * Preview image height
     */
    const PREVIEW_IMAGE_HEIGHT = 200;

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
            'magento_version_from' => $themeVersions['from'],
            'magento_version_to'   => $themeVersions['to'],
            'theme_path'           => $packageCode . '/' . $themeCode
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
        $this->removePreviewImage();
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

    /**
     * Get themes root directory absolute path
     *
     * @return string
     */
    protected static function _getPreviewImagePublishedRootDir()
    {
        $fileSystemHelper = new Varien_Io_File();
        $dirPath = Mage::getBaseDir('media') . DS . self::THEME_DIR;
        $fileSystemHelper->checkAndCreateFolder($dirPath);
        return $dirPath;
    }

    /**
     * Get directory path for origin image
     *
     * @return string
     */
    public static function getImagePathOrigin()
    {
        return self::_getPreviewImagePublishedRootDir() . DS . self::IMAGE_DIR_ORIGIN;
    }

    /**
     * Get directory path for preview image
     *
     * @return string
     */
    protected static function _getImagePathPreview()
    {
        return self::_getPreviewImagePublishedRootDir() . DS . self::IMAGE_DIR_PREVIEW;
    }

    /**
     * Get preview image directory url
     *
     * @return string
     */
    public static function getPreviewImageDirectoryUrl()
    {
        return Mage::getBaseUrl('media') . self::THEME_DIR . '/' . self::IMAGE_DIR_PREVIEW . '/';
    }

    /**
     * Save data from form
     *
     * @param array $themeData
     * @return Mage_Core_Model_Theme
     */
    public function saveFormData($themeData)
    {
        if (isset($themeData['theme_id'])) {
            $this->load($themeData['theme_id']);
        }
        $previewImageData = array();
        if (isset($themeData['preview_image'])) {
            $previewImageData = $themeData['preview_image'];
            unset($themeData['preview_image']);
        }
        $this->addData($themeData);

        if (isset($previewImageData['delete'])) {
            $this->removePreviewImage();
        }

        $this->uploadPreviewImage('preview_image');
        $this->save();
        return $this;
    }

    /**
     * Upload and create preview image
     *
     * @throws Mage_Core_Exception
     * @param string $scope the request key for file
     * @return bool
     */
    public function uploadPreviewImage($scope)
    {
        $adapter  = new Zend_File_Transfer_Adapter_Http();
        if (!$adapter->isUploaded($scope)) {
            return false;
        }
        if (!$adapter->isValid($scope)) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Uploaded image is not valid'));
        }
        $upload = new Varien_File_Uploader($scope);
        $upload->setAllowCreateFolders(true);
        $upload->setAllowedExtensions(array('jpg', 'gif', 'png'));
        $upload->setAllowRenameFiles(true);
        $upload->setFilesDispersion(false);

        if (!$upload->save(self::getImagePathOrigin())) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Image can not be saved.'));
        }
        $fileName = $upload->getUploadedFileName();
        $this->createPreviewImage($fileName);
        return true;
    }

    /**
     * Create preview image
     *
     * @param string $originalImageName
     * @return string
     */
    public function createPreviewImage($originalImageName)
    {
        $imagePath = self::getImagePathOrigin() . DS . $originalImageName;
        $imageName = str_replace(DS, '_', $this->getThemeCode()) . '.jpg';

        $adapter = Mage::helper('Mage_Core_Helper_Data')->getImageAdapterType();
        $image = new Varien_Image($imagePath, $adapter);
        $image->keepTransparency(true);
        $image->constrainOnly(true);
        $image->keepFrame(true);
        $image->keepAspectRatio(true);
        $image->backgroundColor(array(255, 255, 255));
        $image->resize(self::PREVIEW_IMAGE_WIDTH, self::PREVIEW_IMAGE_HEIGHT);
        $image->save(self::_getImagePathPreview(), $imageName);

        $io = new Varien_Io_File();
        $io->open(array('path' => self::getImagePathOrigin()));
        if ($io->fileExists($originalImageName)) {
            $io->rm($originalImageName);
        }
        $this->setPreviewImage($imageName);
        return $imageName;
    }

    /**
     * Delete preview image
     *
     * @return Mage_Core_Model_Theme
     */
    public function removePreviewImage()
    {
        $previewImage = $this->getPreviewImage();
        $this->setPreviewImage(null);
        if (!$previewImage) {
            return $this;
        }

        $io = new Varien_Io_File();
        $io->open(array('path' => self::_getImagePathPreview()));
        if ($io->fileExists($previewImage)) {
            $io->rm($previewImage);
        }
        return $this;
    }

    /**
     * Get url for themes preview image
     *
     * @return string
     */
    public function getPreviewImageUrl()
    {
        if (!$this->getPreviewImage()) {
            return $this->_getPreviewImageDefaultUrl();
        }
        return self::getPreviewImageDirectoryUrl() . $this->getPreviewImage();
    }

    /**
     * Return default themes preview image url
     *
     * @return string
     */
    protected function _getPreviewImageDefaultUrl()
    {
        return Mage::getDesign()->getSkinUrl('Mage_Core::theme/default_preview.jpg');
    }
}
