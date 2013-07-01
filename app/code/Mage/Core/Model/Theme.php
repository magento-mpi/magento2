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
 * @method string getPackageCode()
 * @method string getParentThemePath()
 * @method string getParentId()
 * @method string getThemeTitle()
 * @method string getThemeVersion()
 * @method string getPreviewImage()
 * @method string getMagentoVersionFrom()
 * @method string getMagentoVersionTo()
 * @method bool getIsFeatured()
 * @method int getThemeId()
 * @method int getType()
 * @method array getAssignedStores()
 * @method Mage_Core_Model_Resource_Theme_Collection getCollection()
 * @method Mage_Core_Model_Theme setAssignedStores(array $stores)
 * @method Mage_Core_Model_Theme addData(array $data)
 * @method Mage_Core_Model_Theme setParentId(int $id)
 * @method Mage_Core_Model_Theme setParentTheme($parentTheme)
 * @method Mage_Core_Model_Theme setPackageCode(string $packageCode)
 * @method Mage_Core_Model_Theme setThemeCode(string $themeCode)
 * @method Mage_Core_Model_Theme setThemePath(string $themePath)
 * @method Mage_Core_Model_Theme setThemeVersion(string $themeVersion)
 * @method Mage_Core_Model_Theme setArea(string $area)
 * @method Mage_Core_Model_Theme setThemeTitle(string $themeTitle)
 * @method Mage_Core_Model_Theme setMagentoVersionFrom(string $versionFrom)
 * @method Mage_Core_Model_Theme setMagentoVersionTo(string $versionTo)
 * @method Mage_Core_Model_Theme setType(string $type)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Core_Model_Theme extends Mage_Core_Model_Abstract
    implements Mage_Core_Model_ThemeInterface, Mage_Core_Model_Theme_Customization_CustomizedInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventPrefix = 'theme';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventObject = 'theme';

    /**
     * @var Mage_Core_Model_Resource_Theme_File_CollectionFactory
     */
    protected $_fileFactory;

    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Array of theme customizations for save
     *
     * @var array
     */
    protected $_themeCustomizations = array();

    /**
     * @var Mage_Core_Model_Theme_ServiceProxy
     */
    protected $_themeService;

    /**
     * @var Mage_Core_Model_Theme_FlyweightFactory
     */
    protected $_domainFactory;

    /**
     * @var Mage_Core_Model_Theme_ImageFactory
     */
    protected $_imageFactory;

    /**
     * @var Mage_Core_Model_Theme_Validator
     */
    protected $_validator;

    /**
     * @var Mage_Core_Model_Resource_Theme_File_Collection
     */
    protected $_themeFiles;

    /**
     * All possible types of a theme
     *
     * @var array
     */
    public static $types = array(
        self::TYPE_PHYSICAL,
        self::TYPE_VIRTUAL,
        self::TYPE_STAGING,
    );

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Context $context
     * @param Mage_Core_Model_Resource_Theme_File_CollectionFactory $fileFactory
     * @param Mage_Core_Model_Theme_FlyweightFactory $themeFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_ServiceProxy $themeService
     * @param Mage_Core_Model_Theme_Domain_Factory $domainFactory
     * @param Mage_Core_Model_Theme_ImageFactory $imageFactory
     * @param Mage_Core_Model_Theme_Validator $validator
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Resource_Theme $resource
     * @param Mage_Core_Model_Resource_Theme_Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Mage_Core_Model_Resource_Theme_File_CollectionFactory $fileFactory,
        Mage_Core_Model_Theme_FlyweightFactory $themeFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Theme_ServiceProxy $themeService,
        Mage_Core_Model_Theme_Domain_Factory $domainFactory,
        Mage_Core_Model_Theme_ImageFactory $imageFactory,
        Mage_Core_Model_Theme_Validator $validator,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Resource_Theme $resource,
        Mage_Core_Model_Resource_Theme_Collection $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
        $this->_fileFactory = $fileFactory;
        $this->_themeFactory = $themeFactory;
        $this->_helper = $helper;
        $this->_themeService = $themeService;
        $this->_domainFactory = $domainFactory;
        $this->_imageFactory = $imageFactory;
        $this->_validator = $validator;
        $this->_dirs = $dirs;

        $this->addData(array(
            'type' => self::TYPE_VIRTUAL,
            'area' => Mage_Core_Model_App_Area::AREA_FRONTEND
        ));
    }

    /**
     * Theme model initialization
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Theme');
    }

    /**
     * Get theme image model
     *
     * @return Mage_Core_Model_Theme_Image
     */
    public function getThemeImage()
    {
        return $this->_imageFactory->create(array('theme' => $this));
    }

    /**
     * Check if theme is deletable
     *
     * @return bool
     */
    public function isDeletable()
    {
        return $this->isEditable();
    }

    /**
     * Check if theme is editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return self::TYPE_PHYSICAL != $this->getType();
    }

    /**
     * Check if theme is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getType() == self::TYPE_VIRTUAL;
    }

    /**
     * Check if theme is physical
     *
     * @return bool
     */
    public function isPhysical()
    {
        return $this->getType() == self::TYPE_PHYSICAL;
    }

    /**
     * Check theme is visible in backend
     *
     * @return bool
     */
    public function isVisible()
    {
        return in_array($this->getType(), array(self::TYPE_PHYSICAL, self::TYPE_VIRTUAL));
    }

    /**
     * Check is theme has child virtual themes
     *
     * @return bool
     */
    public function hasChildThemes()
    {
        return (bool)$this->getCollection()
            ->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL)
            ->addFieldToFilter('parent_id', array('eq' => $this->getId()))
            ->getSize();
    }

    /**
     * Get directory where themes files are stored
     *
     * @return string
     */
    public function getThemeFilesPath()
    {
        if ($this->getType() == self::TYPE_PHYSICAL) {
            $physicalThemesDir = $this->_dirs->getDir(Mage_Core_Model_Dir::THEMES);
            $dir = sprintf('%s/%s', $physicalThemesDir, $this->getFullPath());
        } else {
            $dir = $this->getCustomizationPath();
        }
        return $dir;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null
     */
    public function getCustomizationPath()
    {
        $customPath = $this->getData('customization_path');
        if ($this->getId() && empty($customPath)) {
            $customPath = $this->_dirs->getDir(Mage_Core_Model_Dir::MEDIA)
                . Magento_Filesystem::DIRECTORY_SEPARATOR . 'theme_customization'
                . Magento_Filesystem::DIRECTORY_SEPARATOR . $this->getId();
            $this->setData('customization_path', $customPath);
        }
        return $customPath;
    }

    /**
     * Retrieve collection of files that belong to a theme
     *
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function getFiles()
    {
        if (!$this->_themeFiles) {
            $this->_themeFiles = $this->_fileFactory->create();
            $this->_themeFiles->addThemeFilter($this);
        }
        return $this->_themeFiles;
    }

    /**
     * Retrieve theme instance representing the latest changes to a theme
     *
     * @return Mage_Core_Model_Theme|null
     */
    public function getStagingVersion()
    {
        if ($this->getId()) {
            $collection = $this->getCollection();
            $collection->addFieldToFilter('parent_id', $this->getId());
            $collection->addFieldToFilter('type', self::TYPE_STAGING);
            $stagingTheme = $collection->getFirstItem();
            if ($stagingTheme->getId()) {
                return $stagingTheme;
            }
        }
        return null;
    }

    /**
     * Return theme customization collection by type
     *
     * @param string $type
     * @return Varien_Data_Collection
     * @throws InvalidArgumentException
     */
    public function getCustomizationData($type)
    {
        if (!isset($this->_themeCustomizations[$type])) {
            throw new InvalidArgumentException('Customization is not present');
        }
        return $this->_themeCustomizations[$type]->getCollectionByTheme($this);
    }

    /**
     * Add theme customization
     *
     * @param Mage_Core_Model_Theme_Customization_CustomizationInterface $customization
     * @return Mage_Core_Model_Theme
     */
    public function setCustomization(Mage_Core_Model_Theme_Customization_CustomizationInterface $customization)
    {
        $this->_themeCustomizations[$customization->getType()] = $customization;
        return $this;
    }

    /**
     * Save all theme customization object
     *
     * @return Mage_Core_Model_Theme
     */
    public function saveThemeCustomization()
    {
        /** @var $file Mage_Core_Model_Theme_Customization_CustomizationInterface */
        foreach ($this->_themeCustomizations as $file) {
            $file->saveData($this);
        }
        return $this;
    }

    /**
     * Check if theme object data was changed.
     *
     * @return bool
     */
    public function hasDataChanges()
    {
        return parent::hasDataChanges() || $this->isCustomized();
    }

    /**
     * Check whether present customization objects
     *
     * @return bool
     */
    public function isCustomized()
    {
        return !empty($this->_themeCustomizations);
    }

    /**
     * Validate theme data
     *
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    protected function _validate()
    {
        if (!$this->_validator->validate($this)) {
            $messages = $this->_validator->getErrorMessages();
            throw new Mage_Core_Exception(implode(PHP_EOL, reset($messages)));
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
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        if (!$this->isDeletable() || $this->_themeService->isThemeAssignedToStore($this)) {
            throw new Mage_Core_Exception($this->_helper->__('Theme isn\'t deletable.'));
        }
        return parent::_beforeDelete();
    }

    /**
     * Update all relations after deleting theme
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $this->saveThemeCustomization();
        if ($this->_themeService->isThemeAssignedToStore($this)) {
            $this->_eventDispatcher->dispatch('assigned_theme_changed', array($this->_eventObject => $this));
        }
        return parent::_afterSave();
    }

    /**
     * Update all relations after deleting theme
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $stagingVersion = $this->getStagingVersion();
        if ($stagingVersion) {
            $stagingVersion->delete();
        }
        $this->getCollection()->updateChildRelations($this);
        return parent::_afterDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function getParentTheme()
    {
        if ($this->hasData('parent_theme')) {
            return $this->getData('parent_theme');
        }

        $theme = null;
        if ($this->getParentId()) {
            $theme = $this->_themeFactory->create($this->getParentId());
        }
        $this->setParentTheme($theme);
        return $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        return $this->getData('area');
    }

    /**
     * {@inheritdoc}
     */
    public function getThemePath()
    {
        return $this->getData('theme_path');
    }

    /**
     * Retrieve theme full path which is used to distinguish themes if they are not in DB yet
     *
     * Alternative id looks like "<area>/<package_code>/<theme_code>".
     * Used as id in file-system theme collection
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->getArea() . self::PATH_SEPARATOR . $this->getThemePath();
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
                || version_compare($magentoVersion, $this->getMagentoVersionFrom(), '<=')
            ) {
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
            $this->setThemeTitle($this->_helper->__('%s (incompatible version)', $this->getThemeTitle()));
        }
        return $this;
    }

    /**
     * Get one of theme domain models
     *
     * @param int|null $type
     * @return Mage_Core_Model_Theme_Domain_Physical|Mage_Core_Model_Theme_Domain_Virtual|
     * Mage_Core_Model_Theme_Domain_Staging
     * @throws Mage_Core_Exception
     */
    public function getDomainModel($type = null)
    {
        if ($type !== null && $type != $this->getType()) {
            throw new Mage_Core_Exception($this->_helper->__(
                'Invalid domain model "%s" requested for theme "%s" of type "%s"',
                $type,
                $this->getId(),
                $this->getType()
            ));
        }

        return $this->_domainFactory->create($this);
    }

    /**
     * Get path to custom view configuration file
     *
     * @return string
     */
    public function getCustomViewConfigPath()
    {
        $config = $this->getCustomizationPath();
        if (!empty($config)) {
            $config .= Magento_Filesystem::DIRECTORY_SEPARATOR . self::FILENAME_VIEW_CONFIG;
        }
        return $config;
    }
}
