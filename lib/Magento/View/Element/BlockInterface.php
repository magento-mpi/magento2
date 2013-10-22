<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento Block interface
 */
namespace Magento\View\Element;

/**
 * @package Magento\View
 */
interface BlockInterface
{
    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml();
}
