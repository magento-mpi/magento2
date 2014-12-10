<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
