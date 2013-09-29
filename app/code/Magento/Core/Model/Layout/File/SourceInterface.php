<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of locating layout files in the file system
 */
namespace Magento\Core\Model\Layout\File;

interface SourceInterface
{
    /**
     * Retrieve instances of layout files
     *
     * @param \Magento\Core\Model\ThemeInterface $theme Theme that defines the design context
     * @return \Magento\Core\Model\Layout\File[]
     */
    public function getFiles(\Magento\Core\Model\ThemeInterface $theme);
}
