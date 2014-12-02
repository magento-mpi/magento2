<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Api\Data;

/**
 * @see \Magento\ConfigurableProduct\Service\V1\Data\Option\Value
 */
interface OptionValueInterface
{
    /**
     * @return float|null
     */
    public function getPrice();

    /**
     * @return int|null
     */
    public function isPercent();

    /**
     * @return int
     */
    public function getIndex();
}
