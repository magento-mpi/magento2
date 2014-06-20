<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

use Magento\Framework\Service\Data\Eav\AttributeValueBuilder;

class OptionValueBuilder extends \Magento\Framework\Service\Data\Eav\AbstractObjectBuilder
{
    /**
     * @var string[]
     */
    protected $customAttributeCodes = [
        'sort_order',
        'title',
        'file_extension',
        'image_size_x',
        'image_size_y',
        'max_characters'
    ];

    /**
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param array $customAttributeCodes
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        array $customAttributeCodes = array()
    ) {
        parent::__construct($objectFactory, $valueBuilder);
        $this->customAttributeCodes = array_merge($this->customAttributeCodes, $customAttributeCodes);
    }

    /**
     * Set price
     *
     * @param float $value
     * @return $this
     */
    public function setPrice($value)
    {
        return $this->_set(OptionValue::PRICE, $value);
    }

    /**
     * Set price type
     *
     * @param string $value
     * @return $this
     */
    public function setPriceType($value)
    {
        return $this->_set(OptionValue::PRICE_TYPE, $value);
    }

    /**
     * Set Sku
     *
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(OptionValue::SKU, $value);
    }

    /**
     * Set ID
     * @param int|null $value
     * @return $this
     */
    public function setOptionTypeId($value)
    {
        return $this->_set(OptionValue::ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesCodes()
    {
        return array_merge($this->customAttributeCodes, parent::getCustomAttributesCodes());
    }
}
