<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Acl\Role;

/**
 * Acl Group model
 */
class Group extends \Magento\User\Model\Acl\Role\Generic
{
    /**
     * All the group roles are prepended by G
     *
     */
    const ROLE_TYPE = 'G';
}
