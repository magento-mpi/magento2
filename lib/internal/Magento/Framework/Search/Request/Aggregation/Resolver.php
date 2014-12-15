<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Search\Request\Aggregation;

class Resolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return false;
    }
}
