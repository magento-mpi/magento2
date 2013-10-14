<?php
/**
 * Magento Block. Used to present information to user
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
