<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

use Magento\Doc\Document\Item;

/**
 * Class AbstractType
 * @package Magento\Doc\Document\Type
 */
abstract class AbstractType
{
    /**
     * Get item's content
     *
     * @param Item $item
     * @return string
     */
    abstract public function getContent(Item $item);
}
