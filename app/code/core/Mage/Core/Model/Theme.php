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
 * @method string getPackageCode()
 * @method string getThemePath()
 * @method string getParentThemePath()
 * @method string getPreviewImage()
 * @method string getThemeDirectory()
 * @method string getParentId()
 * @method Mage_Core_Model_Theme setParentId(int $id)
 * @method Mage_Core_Model_Theme setParentTheme($parentTheme)
 * @method Mage_Core_Model_Theme setPackageCode(string $packageCode)
 * @method Mage_Core_Model_Theme setThemeCode(string $themeCode)
 * @method Mage_Core_Model_Theme setPreviewImage(string $previewImage)
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
     * Labels collection array
     *
     * @var array
     */
    protected $_labelsCollection = null;

    /**
     * @var Varien_Io_File
     */
    protected $_ioFile;

    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Filesystem client
     *
     * @return Varien_Io_File
     */
    protected function _getIoFile()
    {
        if (!$this->_ioFile) {
            $this->_ioFile = new Varien_Io_File();
        }
        return $this->_ioFile;
    }

    /**
     * Themes collection loaded from file system configurations
     *
     * @return Mage_Core_Model_Theme_Collection
     */
    public function getCollectionFromFilesystem()
    {
        return Mage::getSingleton('Mage_Core_Model_Theme_Collection');
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
        $parentTheme = $themeConfig->getParentTheme($packageCode, $themeCode);
        $this->setData(array(
            'parent_id'            => null,
            'theme_path'           => $packageCode . '/' . $themeCode,
            'theme_version'        => $themeConfig->getThemeVersion($packageCode, $themeCode),
            'theme_title'          => $themeConfig->getThemeTitle($packageCode, $themeCode),
            'preview_image'        => $media['preview_image'] ? $media['preview_image'] : null,
            'magento_version_from' => $themeVersions['from'],
            'magento_version_to'   => $themeVersions['to'],
            'is_featured'          => $themeConfig->getFeatured($packageCode, $themeCode),
            'theme_directory'      => dirname($configPath),
            'parent_theme_path'    => $parentTheme ? implode('/', $parentTheme) : null
        ));
        $this->getArea();
        $this->_updateDefaultParams();

        return $this;
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        if ($this->getId()) {
            $this->_updateDefaultParams();
        }
        return parent::_afterLoad();
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
     * Check is theme deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        return $this->isVirtual();
    }

    /**
     * Check theme is existing in filesystem
     *
     * @return bool
     */
    public function isVirtual()
    {
        $collection = $this->getCollectionFromFilesystem()->addDefaultPattern('*')->getItems();
        return !($this->getThemePath() && isset($collection[$this->getTempId()]));
    }

    /**
     * Check is theme has child themes
     *
     * @return bool
     */
    public function hasChildThemes()
    {
        $childThemes = $this->getCollection()->addFieldToFilter('parent_id', array('eq' => $this->getId()))->load();
        return count($childThemes) > 0;
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
     * @return Mage_Core_Model_Theme
     */
    protected function _beforeSave()
    {
        $this->_validate();
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
     * Get parent theme model
     *
     * @return Mage_Core_Model_Theme|null
     */
    public function getParentTheme()
    {
        if ($this->hasData('parent_theme')) {
            return $this->getData('parent_theme');
        }

        $theme = null;
        if ($this->getParentId()) {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = Mage::getModel('Mage_Core_Model_Theme');
            $theme->load($this->getParentId());
        }
        $this->setParentTheme($theme);

        return $theme;
    }

    /**
     * Save preview image
     *
     * @return Mage_Core_Model_Theme
     */
    public function savePreviewImage()
    {
        if (!$this->getPreviewImage() || !$this->getThemeDirectory()) {
            return $this;
        }
        $currentWorkingDir = getcwd();

        chdir($this->getThemeDirectory());

        $imagePath = realpath($this->getPreviewImage());

        if (0 === strpos($imagePath, $this->getThemeDirectory())) {
            $this->createPreviewImage($imagePath);
        }

        chdir($currentWorkingDir);

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
        $upload->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'xbm', 'wbmp'));
        $upload->setAllowRenameFiles(true);
        $upload->setFilesDispersion(false);

        if (!$upload->save(self::getImagePathOrigin())) {
            Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Image can not be saved.'));
        }

        $fileName = self::getImagePathOrigin() . DS . $upload->getUploadedFileName();
        $this->removePreviewImage();
        $this->createPreviewImage($fileName);

        $this->_getIoFile()->rm($fileName);

        return true;
    }

    /**
     * Create preview image
     *
     * @param string $imagePath
     * @return string
     */
    public function createPreviewImage($imagePath)
    {
        $adapter = Mage::helper('Mage_Core_Helper_Data')->getImageAdapterType();
        $image = new Varien_Image($imagePath, $adapter);
        $image->keepTransparency(true);
        $image->constrainOnly(true);
        $image->keepFrame(true);
        $image->keepAspectRatio(true);
        $image->backgroundColor(array(255, 255, 255));
        $image->resize(self::PREVIEW_IMAGE_WIDTH, self::PREVIEW_IMAGE_HEIGHT);

        $imageName = uniqid('preview_image_') . image_type_to_extension($image->getMimeType());
        $image->save(self::_getImagePathPreview(), $imageName);

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
        $this->setPreviewImage('');
        if ($previewImage) {
            $this->_getIoFile()->rm(self::_getImagePathPreview() . DS . $previewImage);
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
        return Mage::getDesign()->getViewFileUrl('Mage_Core::theme/default_preview.jpg');
    }

    /**
     * Get theme area by theme path
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getArea()
    {
        if (!$this->getData('area')) {
            $themeDirectory = $this->getThemeDirectory();
            $baseDesignDirectory = Mage::getBaseDir('design');
            if (substr($themeDirectory, 0, strlen($baseDesignDirectory)) != $baseDesignDirectory) {
                Mage::throwException(
                    Mage::helper('Mage_Core_Helper_Data')->__('Invalid theme directory "%s"', $themeDirectory)
                );
            }
            $themeDirectory = substr($themeDirectory, strlen($baseDesignDirectory));
            if (substr($themeDirectory, 0, 1) == DS) {
                $themeDirectory = substr($themeDirectory, 1);
            }
            list($area,) = explode(DS, $themeDirectory, 2);
            $this->setData('area', $area);
        }

        return $this->getData('area');
    }

    /**
     * Retrieve alternative id which is used to distinguis themes if they are not in DB yet
     * Looks like "<area>/<package_code>/<theme_code>".
     * Used as id in file-system theme collection
     *
     * @return string
     */
    public function getTempId()
    {
        return $this->getArea() . '/' . $this->getThemePath();
    }

    /**
     * Update default params (package_code and theme_code)
     *
     * @return Mage_Core_Model_Theme
     */
    protected function _updateDefaultParams()
    {
        list($packageCode, $themeCode) = $this->getThemePath() ? explode('/', $this->getThemePath()) : null;
        $this->setPackageCode($packageCode)->setThemeCode($themeCode);
        return $this;
    }

    /**
     * Check if the theme is compatible with Magento version
     *
     * @return bool
     */
    public function isThemeCompatible()
    {
        $magentoVersion = Mage::getVersion();
        if (version_compare($magentoVersion, $this->getMagentoVersionFrom(), '>=')) {
            if ($this->getMagentoVersionTo() == '*'
                || version_compare($magentoVersion, $this->getMagentoVersionFrom(), '<=')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the theme is compatible with Magento version and mark theme label if not compatible
     *
     * @return Mage_Core_Model_Theme
     */
    public function checkThemeCompatible()
    {
        if (!$this->isThemeCompatible()) {
            $this->setThemeTitle(
                Mage::helper('Mage_Core_Helper_Data')->__('%s (incompatible version)', $this->getThemeTitle())
            );
        }
        return $this;
    }

    /**
     * Return labels collection array
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @return array
     */
    public function getLabelsCollection($withEmpty = true)
    {
        if (!$this->_labelsCollection) {
            /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
            $themeCollection = $this->getCollection();
            $themeCollection->setOrder('theme_title', Varien_Data_Collection::SORT_ORDER_ASC)
                ->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
                ->walk('checkThemeCompatible');
            $this->_labelsCollection = $themeCollection->toOptionArray();
        }
        $options = $this->_labelsCollection;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('-- Please Select --'))
            );
        }
        return $options;
    }

    /**
     * Load object data by alternative "id"
     *
     * @param string $tempId
     * @return Mage_Core_Model_Theme
     */
    public function loadByTempId($tempId)
    {
        $this->_beforeLoad($tempId, 'area_theme_path');
        $this->_getResource()->loadByTempId($this, $tempId);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }
}
