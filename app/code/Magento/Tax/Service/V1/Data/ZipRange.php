<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

class ZipRange extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_FROM = 'from';

    const KEY_TO = 'to';

    /**#@-*/

    /**
     * Get zip range starting point
     *
     * @return int
     */
    public function getFrom()
    {
        return $this->_get(self::KEY_FROM);
    }

    /**
     * Get zip range ending point
     *
     * @return int
     */
    public function getTo()
    {
        return $this->_get(self::KEY_TO);
    }
}
