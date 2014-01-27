<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

interface ScopeInterface extends \Magento\App\Config\ScopeInterface
{
    /**
     * Retrieve base URL
     *
     * @param string $type
     * @param boolean|null $secure
     * @return string
     */
    public function getBaseUrl($type = '', $secure = null);

    /**
     * Retrieve scope configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function getConfig($path);

    /**
     * Check is URL should be secure
     *
     * @return boolean
     */
    public function isUrlSecure();
}
