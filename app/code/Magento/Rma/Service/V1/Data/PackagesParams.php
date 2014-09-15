<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

class PackagesParams extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const CONTAINER = 'container';
    const WEIGHT = 'weight';
    const CUSTOMS_VALUE = 'customs_value';
    const LENGTH = 'length';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const WEIGHT_UNITS = 'weight_units';
    const DIMENSION_UNITS = 'dimension_units';
    const CONTENT_TYPE = 'content_type';
    const CONTENT_TYPE_OTHER = 'content_type_other';
    const DELIVERY_CONFIRMATION = 'delivery_confirmation';
    /**#@-*/

    /**
     * Get container
     *
     * @return string
     */
    public function getContainer()
    {
        return $this->_get(self::CONTAINER);
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_get(self::WEIGHT);
    }

    /**
     * Get customs_value
     *
     * @return string
     */
    public function getCustomsValue()
    {
        return $this->_get(self::CUSTOMS_VALUE);
    }

    /**
     * Get length
     *
     * @return string
     */
    public function getLength()
    {
        return $this->_get(self::LENGTH);
    }

    /**
     * Get width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->_get(self::WIDTH);
    }

    /**
     * Get height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->_get(self::HEIGHT);
    }

    /**
     * Get weight_units
     *
     * @return string
     */
    public function getWeightUnits()
    {
        return $this->_get(self::WEIGHT_UNITS);
    }

    /**
     * Get dimension_units
     *
     * @return string
     */
    public function getDimensionUnits()
    {
        return $this->_get(self::DIMENSION_UNITS);
    }

    /**
     * Get content_type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_get(self::CONTENT_TYPE);
    }

    /**
     * Get content_type_other
     *
     * @return string
     */
    public function getContentTypeOther()
    {
        return $this->_get(self::CONTENT_TYPE_OTHER);
    }

    /**
     * Get delivery_confirmation
     *
     * @return int
     */
    public function getDeliveryConfirmation()
    {
        return $this->_get(self::DELIVERY_CONFIRMATION);
    }
}
