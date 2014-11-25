<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

class Type extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterface
{
    /**
     * Get option type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * Get option type code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * Get option type group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->getData('group');
    }
}
