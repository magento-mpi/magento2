<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Api\Data;

interface OptionValueInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return float|null
     */
    public function getPricingValue();

    /**
     * @return int|null
     */
    public function getIsPercent();

    /**
     * @return int
     */
    public function getValueIndex();
}
