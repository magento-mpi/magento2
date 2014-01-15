<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

interface ConfigInterface
{
    /**
     * Get indexer's config
     *
     * @return array
     */
    public function getAll();

    /**
     * Get config value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
}
