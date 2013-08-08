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
 * Theme factory
 */
class Magento_Core_Model_Theme_FlyweightFactory
{
    /**
     * Object Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Theme[]
     */
    protected $_themes = array();

    /**
     * @var Magento_Core_Model_Theme[]
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
     * @return Magento_Core_Model_Theme|null
     * @throws InvalidArgumentException
     */
    public function create($themeKey, $area = Magento_Core_Model_View_DesignInterface::DEFAULT_AREA)
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
     * @return Magento_Core_Model_Theme
     */
    protected function _loadById($themeId)
    {
        if (isset($this->_themes[$themeId])) {
            return $this->_themes[$themeId];
        }

        /** @var $themeModel Magento_Core_Model_Theme */
        $themeModel = $this->_objectManager->create('Magento_Core_Model_Theme');
        $themeModel->load($themeId);
        return $themeModel;
    }

    /**
     * Load theme by theme path
     *
     * @param string $themePath
     * @param string $area
     * @return Magento_Core_Model_Theme
     */
    protected function _loadByPath($themePath, $area)
    {
        $fullPath = $area . Magento_Core_Model_ThemeInterface::PATH_SEPARATOR . $themePath;
        if (isset($this->_themesByPath[$fullPath])) {
            return $this->_themesByPath[$fullPath];
        }

        /** @var $themeCollection Magento_Core_Model_Resource_Theme_Collection */
        $themeCollection = $this->_objectManager->create('Magento_Core_Model_Resource_Theme_Collection');
        $themeModel = $themeCollection->getThemeByFullPath($fullPath);
        return $themeModel;
    }

    /**
     * Add theme to shared collection
     *
     * @param Magento_Core_Model_Theme $themeModel
     * @return $this
     */
    protected function _addTheme(Magento_Core_Model_Theme $themeModel)
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
