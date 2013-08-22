<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Core_Model_View_DesignInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Set package area
     *
     * @param string $area
     * @return Magento_Core_Model_View_DesignInterface
     */
    public function setArea($area);

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea();

    /**
     * Set theme path
     *
     * @param Magento_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Magento_Core_Model_View_DesignInterface
     */
    public function setDesignTheme($theme, $area = null);

    /**
     * Get default theme which declared in configuration
     *
     * @param string $area
     * @param array $params
     * @return string|int
     */
    public function getConfigurationDesignTheme($area = null, array $params = array());

    /**
     * Set default design theme
     *
     * @return Magento_Core_Model_View_DesignInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Magento_Core_Model_Theme
     */
    public function getDesignTheme();

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams();
}
