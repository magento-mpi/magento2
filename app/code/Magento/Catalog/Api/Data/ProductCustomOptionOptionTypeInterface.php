<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionType - previous implementation
 *
 * @todo Create new model \Magento\Catalog\Model\Product\CustomOption\OptionType implements \Magento\Catalog\Api\Data\Product\CustomOption\OptionTypeInterface
 */
interface ProductCustomOptionOptionTypeInterface
{
    /**
     * Get option type label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get option type code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get option type group
     *
     * @return string
     */
    public function getGroup();
}
