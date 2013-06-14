<?php
/**
 * Authorization interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_AuthorizationInterface
{
    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null);
}
