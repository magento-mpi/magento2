<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface;

class Builder implements BuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(FilterInterface $filter)
    {
        return sprintf('(%s)', '1');
    }
}
