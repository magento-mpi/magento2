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
 * Customer identity interface.
 */
interface IdentityInterface
{
    /**
     * Get customer email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get website ID.
     *
     * @return int|null
     */
    public function getWebsiteId();
}
