<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Field;

interface ResolverInterface
{
    /**
     * Resolve field
     *
     * @param string|array $fields
     * @return string|array
     */
    public function resolve($fields);
}
