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
 * @see \Magento\ConfigurableProduct\Service\V1\Data\Option
 */
interface OptionInterface
{
    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getAttributeId();

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @return int|null
     */
    public function getPosition();

    /**
     * @return bool|null
     */
    public function isUseDefault();

    /**
     * @return \Magento\ConfigurableProduct\Api\Data\OptionValueInterface[]|null
     */
    public function getValues();
}
