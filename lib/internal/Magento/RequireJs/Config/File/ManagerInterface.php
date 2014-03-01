<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Config\File;

/**
 * File manager for working with RequireJs config file
 */
interface ManagerInterface
{
    /**
     * Get absolute path to RequireJs config file
     *
     * @return string
     */
    public function getConfigFile();
}
