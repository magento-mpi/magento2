<?php
/**
 * Loader interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Config;

interface LoaderInterface
{
    /**
     * Load configuration for current scope
     */
    public function load();
}
