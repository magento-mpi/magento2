<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Field;

use Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface;

class Resolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve($fields)
    {
        return 'data_index';
    }
}
