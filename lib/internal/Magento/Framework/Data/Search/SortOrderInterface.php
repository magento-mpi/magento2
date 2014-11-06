<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface SortOrderInterface 
{
    /**
     * Get sorting field.
     *
     * @return string
     */
    public function getField();

    /**
     * Get sorting direction.
     *
     * @return string
     */
    public function getDirection();
}
