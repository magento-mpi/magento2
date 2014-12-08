<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

class Reader implements ReaderInterface
{
    /**
     * @var ReaderInterface[]
     */
    protected $valueReaders;

    /**
     * @param ReaderInterface[] $valueReaders
     */
    public function __construct($valueReaders)
    {
        $this->valueReaders = $valueReaders;
    }

    /**
     * Load option value
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return Metadata[]
     */
    public function read(\Magento\Catalog\Model\Product\Option $option)
    {
        $type = $option->getType();
        $reader = isset($this->valueReaders[$type]) ? $this->valueReaders[$type] : $this->valueReaders['default'];
        return $reader->read($option);
    }
}
