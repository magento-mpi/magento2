<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Customer group interface.
 */
interface Group extends ExtensibleDataInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get tax class id
     *
     * @return int
     */
    public function getTaxClassId();
}
