<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product\Option;

use Magento\Framework\Api\AbstractExtensibleObjectBuilder;

/**
 * @codeCoverageIgnore
 */
class TypeBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set type label
     *
     * @param int $value
     * @return $this
     */
    public function setLabel($value)
    {
        return $this->_set(Type::LABEL, $value);
    }

    /**
     * Set type code
     *
     * @param int $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->_set(Type::CODE, $value);
    }
}
