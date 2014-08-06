<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Email\Container\Stub;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ScopeConfigInterfaceMock implements ScopeConfigInterface
{
    public function getValue($path, $scope = \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return;
    }

    public function isSetFlag($path, $scope = \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return;
    }
}
