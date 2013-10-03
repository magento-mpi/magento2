<?php
/**
 * The user is an abstraction for retrieving credentials for Authentication and validating Authorization
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound;

interface UserInterface
{
    /**
     * Returns a shared secret known only by Magento and this user
     *
     * @return string a shared secret that both the user and Magento know about
     */
    public function getSharedSecret();

    /**
     * Checks whether this user has permission for the given topic
     *
     * @param string $topic topic to check
     * @return bool TRUE if permissions exist
     */
    public function hasPermission($topic);
}
