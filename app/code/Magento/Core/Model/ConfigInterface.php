<?php
/**
 * Configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

interface ConfigInterface
{
    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = 'default', $scopeCode = null);

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     */
    public function setValue($path, $value, $scope = 'default', $scopeCode = null);
}
