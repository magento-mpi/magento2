<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product\Link;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class MetadataBuilder extends AbstractObjectBuilder
{
    /**
     * @param string $value
     * @return $this
     */
    public function setSku($value)
    {
        return $this->_set(Metadata::SKU, $value);
    }

    /**
     * @param float $value
     * @return $this
     */
    public function setQty($value)
    {
        return $this->_set(Metadata::QTY, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        return $this->_set(Metadata::POSITION, $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setOptionId($value)
    {
        return $this->_set(Metadata::OPTION_ID, $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setDefined($value)
    {
        return $this->_set(Metadata::DEFINED, (bool)$value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setDefault($value)
    {
        return $this->_set(Metadata::IS_DEFAULT, (bool)$value);
    }
}
