<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

interface BuilderInterface
{
    /**
     * @param RequestFilterInterface $filter
     * @return string
     */
    public function build(RequestFilterInterface $filter);
}
