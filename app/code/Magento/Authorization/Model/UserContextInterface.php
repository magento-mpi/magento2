<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Model;

/**
 * Interface for current user identification.
 */
interface UserContextInterface
{
    /**
     * Identify current user ID.
     *
     * @return int|null
     */
    public function getUserId();

    /**
     * Retrieve current user type (Admin, Customer, Guest, Integration).
     *
     * @return string
     */
    public function getUserType();
}
