<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface;

interface BuilderInterface
{
    /**
     * @param FilterInterface $filter
     * @return string
     */
    public function build(FilterInterface $filter);
}
