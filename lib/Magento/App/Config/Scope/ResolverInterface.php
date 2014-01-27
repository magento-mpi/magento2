<?php
/**
 * Resolver interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

interface ResolverInterface
{
    /**
     * Retrieve application scope code
     *
     * @param null|int $scopeId
     * @return strings
     */
    public function getScopeCode($scopeId = null);
}
