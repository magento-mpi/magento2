<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Search;


/**
 * Groups two or more filters together using a logical group type
 */
interface FilterGroupInterface
{
    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Customer\Service\V1\Dto\Filter[]
     */
    public function getFilters();

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface[]
     */
    public function getGroups();

    /**
     * Returns the grouping type such as 'OR' or 'AND'.
     *
     * @return string
     */
    public function getGroupType();
}
