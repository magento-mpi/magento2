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
 * Minimal required interface a theme has to implement
 */
namespace Magento\View\Design;

use Magento\Core\Model\ThemeInterface;

interface Theme extends ThemeInterface
{
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
     * @return \Magento\Core\Model\ThemeInterface|null
     */
    public function getParentTheme();

    /**
     * Get code of the theme
     *
     * @return string
     */
    public function getCode();

    public function isPhysical();
}
