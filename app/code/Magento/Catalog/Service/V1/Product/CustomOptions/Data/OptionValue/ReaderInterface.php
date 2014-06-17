<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

interface ReaderInterface
{
    /**
     * Read product option custom attributes value
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue[]
     */
    public function read(\Magento\Catalog\Model\Product\Option $option);
}
