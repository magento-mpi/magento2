<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Design\Theme;

interface DataProviderInterface
{
    /**
     * @param string $fullPath
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getByFullPath($fullPath);

    /**
     * @param int $themeId
     * @return \Magento\View\Design\ThemeInterface
     */
    public function getById($themeId);
}
