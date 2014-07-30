<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Doc\Document\Type;

abstract class AbstractType
{
    /**
     * Get item's content
     *
     * @param string $filePath
     * @param array $item
     * @return string
     */
    abstract public function getContent($filePath, $item);
}
