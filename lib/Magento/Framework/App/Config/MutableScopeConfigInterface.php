<?php
/**
 * Configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\Config;

interface MutableScopeConfigInterface extends \Magento\Framework\App\Config\ScopeConfigInterface
{
    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     * @return void
     */
    public function setValue(
        $path,
        $value,
        $scope = \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT,
        $scopeCode = null
    );
}
