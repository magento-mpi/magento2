<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

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
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get($path = null, $default = null);
}
