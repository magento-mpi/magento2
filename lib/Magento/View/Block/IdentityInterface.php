<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Block;

/**
 * Interface IdentityInterface
 * @package Magento\View\Block
 */
interface IdentityInterface
{
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities();
}