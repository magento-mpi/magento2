<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme customization configuration interface
 */
namespace Magento\View\Design\Theme\Customization;

interface ConfigInterface
{
    /**
     * Get customization file types
     *
     * @return array Mappings of customization file types to its classes
     */
    public function getFileTypes();
}
