<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Data\Option\Type;

use \Magento\Framework\Service\Data\AbstractObject;

/**
 * @codeCoverageIgnore
 */
class Metadata extends AbstractObject
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
