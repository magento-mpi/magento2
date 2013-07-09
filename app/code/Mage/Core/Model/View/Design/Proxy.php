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
 * Design proxy
 */
class Mage_Core_Model_View_Design_Proxy implements Mage_Core_Model_View_DesignInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_View_Design
     */
    protected $_model;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @return Mage_Core_Model_View_Design
     */
    protected function _getInstance()
    {
        if (null === $this->_model) {
            $this->_model = $this->_objectManager->get('Mage_Core_Model_View_Design');
        }
        return $this->_model;
    }

    /**
     * Set package area
     *
     * @param string $area
     * @return Mage_Core_Model_View_DesignInterface
     */
    public function setArea($area)
    {
        return $this->_getInstance()->setArea($area);
    }

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_getInstance()->getArea();
    }

    /**
     * Set theme path
     *
     * @param Mage_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Mage_Core_Model_View_DesignInterface
     */
    public function setDesignTheme($theme, $area = null)
    {
        return $this->_getInstance()->setDesignTheme($theme, $area);
    }

    /**
     * Get default theme which declared in configuration
     *
     * @param string $area
     * @param array $params
     * @return string|int
     */
    public function getConfigurationDesignTheme($area = null, array $params = array())
    {
        return $this->_getInstance()->getConfigurationDesignTheme($area, $params);
    }

    /**
     * Set default design theme
     *
     * @return Mage_Core_Model_View_DesignInterface
     */
    public function setDefaultDesignTheme()
    {
        return $this->_getInstance()->setDefaultDesignTheme();
    }

    /**
     * Design theme model getter
     *
     * @return Mage_Core_Model_Theme
     */
    public function getDesignTheme()
    {
        return $this->_getInstance()->getDesignTheme();
    }

    /**
     * Load design theme
     *
     * @param int|string $themeId
     * @param string $area
     * @return Mage_Core_Model_Theme
     */
    public function loadDesignTheme($themeId, $area = Mage_Core_Model_View_DesignInterface::DEFAULT_AREA)
    {
        return $this->_getInstance()->loadDesignTheme($themeId, $area);
    }

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams()
    {
        return $this->_getInstance()->getDesignParams();
    }
}
