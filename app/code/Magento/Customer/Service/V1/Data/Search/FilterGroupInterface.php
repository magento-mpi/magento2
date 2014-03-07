<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Customer\Service\V1\Data\Filter;

/**
 * Groups two or more filters together using a logical group type
 */
interface FilterGroupInterface
{
    /**
     * Returns a list of filters in this group
     *
     * @return Filter[]
     */
    public function getFilters();

    /**
     * Returns a list of filter groups in this group
     *
     * @return FilterGroupInterface[]
     */
    public function getGroups();

    /**
     * Returns the grouping type such as 'OR' or 'AND'.
     *
     * @return string
     */
    public function getGroupType();
}
