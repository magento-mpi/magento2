<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\LayeredNavigation\Model\Aggregation;

use Magento\Framework\Search\Request\Aggregation\ResolverInterface;

class Resolver implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }
}
