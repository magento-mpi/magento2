<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

use Magento\Framework\Api\Data\ExtensibleDataInterface;

/**
 * Customer group interface.
 */
interface GroupInterface extends ExtensibleDataInterface
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

    /**
     * Get tax class name
     *
     * @return string
     */
    public function getTaxClassName();
}
