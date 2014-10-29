<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Option interface.
 */
interface OptionInterface
{
    /**
     * Get option label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option value
     *
     * @return string|null
     */
    public function getValue();

    /**
     * Get nested options
     *
     * @return \Magento\Customer\Model\Data\Option[]|null
     */
    public function getOptions();
}
