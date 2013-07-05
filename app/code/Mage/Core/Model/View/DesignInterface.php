<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_View_DesignInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Set package area
     *
     * @param string $area
     * @return Mage_Core_Model_View_DesignInterface
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
     * @param Mage_Core_Model_Theme|int|string $theme
     * @param string $area
     * @return Mage_Core_Model_View_DesignInterface
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
     * @return Mage_Core_Model_View_DesignInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Mage_Core_Model_Theme
     */
    public function getDesignTheme();

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams();

    /**
     * Load design theme
     *
     * @param int|string $themeId
     * @param string $area
     * @return mixed
     */
    public function loadDesignTheme($themeId, $area = Mage_Core_Model_View_DesignInterface::DEFAULT_AREA);
}
