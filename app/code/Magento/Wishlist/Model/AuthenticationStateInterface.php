<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Model;

interface AuthenticationStateInterface
{
    /**
     * Is authentication enabled
     *
     * @return bool
     */
    public function isEnabled();
}
