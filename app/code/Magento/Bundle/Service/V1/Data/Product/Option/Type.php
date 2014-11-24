<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Product\Option;

use \Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class Type extends AbstractExtensibleObject
{
    const LABEL = 'label';

    const CODE = 'code';

    /**
     * Get type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get type code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }
}
