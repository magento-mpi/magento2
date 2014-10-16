<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface FilterGroupInterface
{
    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Framework\Data\Search\FilterInterface[]|null
     */
    public function getFilters();
}
 