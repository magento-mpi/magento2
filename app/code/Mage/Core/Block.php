<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento Block interface
 */
interface Mage_Core_Block
{
    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml();
}
