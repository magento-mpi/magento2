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
 * Theme Service model
 */
class Mage_Core_Model_Theme_Service
{
    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;


    /**
     * Whether is present customized themes
     *
     * @var bool
     */
    protected $_hasCustomizedThemes;

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Theme $theme, Mage_Core_Model_Design_Package $design,
        Mage_Core_Model_Config $config
    ) {
        $this->_theme = $theme;
        $this->_design = $design;
        $this->_config = $config;
    }

    /**
     * Assign theme to the stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param string $area
     * @return Mage_Core_Model_Theme_Service
     * @throws UnexpectedValueException
     */
    public function assignThemeToStores($themeId, $stores = array(), $scope = Mage_Core_Model_Config::SCOPE_STORES,
        $area = Mage_Core_Model_Design_Package::DEFAULT_AREA
    ) {
        if (!$this->_theme->load($themeId)->getId()) {
            throw new UnexpectedValueException('Theme doesn\'t recognized. Requested id: ' . $themeId);
        }
        foreach ($stores as $storeId) {
            $this->_config->saveConfig(
                $this->_design->getConfigPathByArea($area), $this->_theme->getId(), $scope, $storeId
            );
        }
        return $this;
    }

    /**
     * Check whether is present customized themes
     *
     * @return bool
     */
    public function isPresentCustomizedThemes()
    {
        if (is_null($this->_hasCustomizedThemes)) {
            $this->_hasCustomizedThemes = false;
            /** @var $theme Mage_Core_Model_Theme */
            foreach ($this->_theme->getCollection() as $theme) {
                if ($theme->isVirtual()) {
                    $this->_hasCustomizedThemes = true;
                    break;
                }
            }
        }
        return $this->_hasCustomizedThemes;
    }

    /**
     * Return not customized theme collection by page
     *
     * @param int $page
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getNotCustomizedFrontThemes($page)
    {
        return $this->_theme->getCollection()
            ->addAreaFilter()
            ->addFilter('theme_path', "theme_path != ''", 'string')
            ->setPageSize()
            ->setCurPage($page);
    }
}
