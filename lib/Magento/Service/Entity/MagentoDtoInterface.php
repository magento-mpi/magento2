<?php
/**
 * Interface that all Magento DTO objects must adhere to.
 *
 * Ensure that classes implementing the interface deeply clone all referenced objects
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

interface MagentoDtoInterface extends LockableInterface
{
    /**
     * Magic method implementation should ensure that all object references are deeply cloned together with DTO.
     *
     * A cloned object will always start off unlocked.
     *
     * @throws \LogicException if data wasn't initialized properly before cloning
     * @return mixed
     */
    public function __clone();

    /**
     * Return DTO data in array format
     *
     * @return \ArrayAccess
     */
    public function __toArray();

}
