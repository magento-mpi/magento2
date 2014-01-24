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
     * Process config value
     *
     * @param string $value
     * @return mixed
     */
    public function processValue($value);
}
