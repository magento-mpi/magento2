<?php
/**
 * Scope Resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

class Resolver implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function processValue($value)
    {
        return $value;
    }

}
