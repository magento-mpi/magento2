<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Design\Theme;

/**
 * Interface FileProviderInterface
 */
interface FileProviderInterface
{
    /**
     * Get items
     *
     * @param \Magento\Framework\View\Design\ThemeInterface $theme
     * @param array $filters
     * @return \Magento\Framework\View\Design\Theme\FileInterface[]
     */
    public function getItems(\Magento\Framework\View\Design\ThemeInterface $theme, array $filters = []);
}
