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
 * Design proxy
 */
class Magento_Core_Model_View_Design_Proxy implements Magento_Core_Model_View_DesignInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_View_Design
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
     * @return Magento_Core_Model_View_Design
     */
    protected function _getInstance()
    {
        if (null === $this->_model) {
            $this->_model = $this->_objectManager->get('Magento_Core_Model_View_Design');
        }
        return $this->_model;
    }

    /**
     * Set package area
     *
     * @param string $area
     * @return Magento_Core_Model_View_DesignInterface
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
     * @param Magento_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Magento_Core_Model_View_DesignInterface
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
     * @return Magento_Core_Model_View_DesignInterface
     */
    public function setDefaultDesignTheme()
    {
        return $this->_getInstance()->setDefaultDesignTheme();
    }

    /**
     * Design theme model getter
     *
     * @return Magento_Core_Model_Theme
     */
    public function getDesignTheme()
    {
        return $this->_getInstance()->getDesignTheme();
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
