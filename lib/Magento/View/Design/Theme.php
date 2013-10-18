<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design;

/**
 * Theme Interface
 *
 * @package Magento\View\Design
 */
interface Theme
{
    /**
     * Separator between theme_path elements
     */
    const PATH_SEPARATOR = '/';

    /**
     * Separator between parts of full theme code (package and theme code)
     */
    const CODE_SEPARATOR = '/';

    /**
     * Retrieve code of an area a theme belongs to
     *
     * @return string
     */
    public function getArea();

    /**
     * Retrieve theme path unique within an area
     *
     * @return string
     */
    public function getThemePath();

    /**
     * Retrieve theme path unique across areas
     *
     * @return string
     */
    public function getFullPath();

    /**
     * Retrieve parent theme instance
     *
     * @return Theme|null
     */
    public function getParentTheme();

    /**
     * Get code of the theme
     *
     * @return string
     */
    public function getCode();

    /**
     * Check if theme is physical
     *
     * @return bool
     */
    public function isPhysical();
}
