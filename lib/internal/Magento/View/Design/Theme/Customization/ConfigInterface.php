<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Customization;

/**
 * Theme customization configuration interface
 */
interface ConfigInterface
{
    /**
     * Get customization file types
     *
     * @return array Mappings of customization file types to its classes
     */
    public function getFileTypes();
}
