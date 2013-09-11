<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\View;

interface DesignInterface
{
    /**
     * Default design area
     */
    const DEFAULT_AREA = 'frontend';

    /**
     * Set package area
     *
     * @param string $area
     * @return \Magento\Core\Model\View\DesignInterface
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
     * @param \Magento\Core\Model\Theme|int|string $theme
     * @param string $area
     * @return \Magento\Core\Model\View\DesignInterface
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
     * @return \Magento\Core\Model\View\DesignInterface
     */
    public function setDefaultDesignTheme();

    /**
     * Design theme model getter
     *
     * @return \Magento\Core\Model\Theme
     */
    public function getDesignTheme();

    /**
     * Get design settings for current request
     *
     * @return array
     */
    public function getDesignParams();
}
