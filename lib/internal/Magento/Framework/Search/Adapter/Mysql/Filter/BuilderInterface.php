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
     * @param string $conditionType
     * @return string
     */
    public function build(RequestFilterInterface $filter, $conditionType);
}
