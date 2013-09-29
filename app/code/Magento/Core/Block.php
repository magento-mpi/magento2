<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento Block interface
 */
namespace Magento\Core;

interface Block
{
    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml();
}
