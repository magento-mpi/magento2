<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Domain;

/**
 * Interface VirtualInterface
 */
interface VirtualInterface
{
    /**
     * Get 'staging' theme
     *
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getStagingTheme();

    /**
     * Get 'physical' theme
     *
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getPhysicalTheme();
}