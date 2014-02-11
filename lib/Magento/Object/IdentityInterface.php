<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Object;

/**
 * Interface IdentityInterface
 * @package Magento\Object
 */
interface IdentityInterface
{
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities();
}