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
 */
interface DesignInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Common node path to theme design configuration
     */
    const XML_PATH_THEME_ID = 'design/theme/theme_id';

    /**
     * Set package area
     *
     * @param string $area
     * @return DesignInterface
     * @deprecated
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
     * @param Design\ThemeInterface|int|string $theme
     * @param string|null $area
     * @return DesignInterface
     */
    public function setDesignTheme($theme, $area = null);

    /**
     * Get default theme which declared in configuration
     *
     * @param string|null $area
     * @param array $params
     * @return string
     */
    public function getConfigurationDesignTheme($area = null, array $params = array());

    /**
     * Set default design theme
     *
     * @return DesignInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return Design\ThemeInterface
     */
    public function getDesignTheme();

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams();
}
