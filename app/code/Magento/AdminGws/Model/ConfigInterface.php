<?php
/**
 * AdminGws configuration model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

interface ConfigInterface
{
    /**
     * Get callback list by group name
     *
     * @param string $groupName
     * @return array
     */
    public function getCallbacks($groupName);

    /**
     * Get deny acl level rules
     *
     * @param string $level
     * @return array
     */
    public function getDeniedAclResources($level);
}
