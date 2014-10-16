<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface FilterInterface 
{
    /**
     * Get field
     *
     * @return string
     */
    public function getField();

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Get condition type
     *
     * @return string|null
     */
    public function getConditionType();
}
