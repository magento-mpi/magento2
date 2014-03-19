<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model;

/**
 * Interface for current user identification.
 */
interface UserLocatorInterface
{
    /**
     * Identify current user ID.
     *
     * @return int
     */
    public function getUserId();

    /**
     * Retrieve current user type (Admin, Customer, Guest, Integration).
     *
     * @return string
     */
    public function getUserType();
}
