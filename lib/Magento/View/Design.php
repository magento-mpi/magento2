<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

/**
 * Design Interface
 *
 * @package Magento\View
 */
interface Design
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Set package area
     *
     * @param string $area
     * @return Design
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
     * @param Design\Theme|int|string $theme
     * @param string $area
     * @return Design
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
     * @return Design
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Design\Theme
     */
    public function getDesignTheme();

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams();
}
