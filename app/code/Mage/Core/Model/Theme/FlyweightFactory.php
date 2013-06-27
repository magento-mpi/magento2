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
 * Theme factory
 */
class Mage_Core_Model_Theme_FlyweightFactory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Theme[]
     */
    protected $_themes = array();

    /**
     * @var Mage_Core_Model_Theme[]
     */
    protected $_themesByPath = array();

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates or returns a shared model of theme
     *
     * @param string|int $themeKey
     * @param string $area
     * @return Mage_Core_Model_Theme|null
     * @throws InvalidArgumentException
     */
    public function create($themeKey, $area = Mage_Core_Model_Design_PackageInterface::DEFAULT_AREA)
    {
        if (is_numeric($themeKey)) {
            $themeModel = $this->_loadById($themeKey);
        } elseif (is_string($themeKey)) {
            $themeModel = $this->_loadByPath($themeKey, $area);
        } else {
            throw new InvalidArgumentException('Incorrect theme identification key');
        }
        if (!$themeModel->getId()) {
            return null;
        }
        $this->_addTheme($themeModel);
        return $themeModel;
    }

    /**
     * Load theme by id
     *
     * @param int $themeId
     * @return Mage_Core_Model_Theme
     */
    protected function _loadById($themeId)
    {
        if (isset($this->_themes[$themeId])) {
            return $this->_themes[$themeId];
        }

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->_objectManager->create('Mage_Core_Model_Theme');
        $themeModel->load($themeId);
        return $themeModel;
    }

    /**
     * Load theme by theme path
     *
     * @param string $themePath
     * @param string $area
     * @return Mage_Core_Model_Theme
     */
    protected function _loadByPath($themePath, $area)
    {
        $fullPath = $area . Mage_Core_Model_ThemeInterface::PATH_SEPARATOR . $themePath;
        if (isset($this->_themesByPath[$fullPath])) {
            return $this->_themesByPath[$fullPath];
        }

        /** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
        $themeCollection = $this->_objectManager->create('Mage_Core_Model_Resource_Theme_Collection');
        $themeModel = $themeCollection->getThemeByFullPath($fullPath);
        return $themeModel;
    }

    /**
     * Add theme to shared collection
     *
     * @param Mage_Core_Model_Theme $themeModel
     * @return $this
     */
    protected function _addTheme(Mage_Core_Model_Theme $themeModel)
    {
        if ($themeModel->getId()) {
            $this->_themes[$themeModel->getId()] = $themeModel;
            $themePath = $themeModel->getFullPath();
            if ($themePath) {
                $this->_themesByPath[$themePath] = $themeModel;
            }
        }
        return $this;
    }
}
