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
interface OptionValueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return float|null
     */
    public function getPrice();

    /**
     * @return int|null
     */
    public function getIsPercent();

    /**
     * @return int
     */
    public function getIndex();
}
