<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Domain;

/**
 * Interface PhysicalInterface
 */
interface PhysicalInterface
{
    /**
     * Create theme customization
     *
     * @param \Magento\View\Design\ThemeInterface $theme
     * @return \Magento\View\Design\ThemeInterface
     */
    public function createVirtualTheme($theme);
}