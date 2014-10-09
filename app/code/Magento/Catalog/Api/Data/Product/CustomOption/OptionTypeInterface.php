<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data\Product\CustomOption;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option
 *
 * @todo Create new model \Magento\Catalog\Model\Product\CustomOption\OptionType implements \Magento\Catalog\Api\Data\Product\CustomOption\OptionTypeInterface
 */
interface OptionTypeInterface
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
