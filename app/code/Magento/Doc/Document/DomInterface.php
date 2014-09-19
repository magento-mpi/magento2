<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document;

/**
 * Interface DomInterface
 * @package Magento\Doc\Document
 */
interface DomInterface
{
    /**
     * Merge $xml into DOM document
     *
     * @param string $xml
     * @return void
     */
    public function merge($xml);
}
