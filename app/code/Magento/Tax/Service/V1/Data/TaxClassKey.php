<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class TaxClassKey
 */
class TaxClassKey extends AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_TYPE = 'type';

    const KEY_VALUE = 'value';
    /**#@-*/

    /**#@+
     * Constants defined for type of tax class key
     */
    const TYPE_ID = 'id';

    const TYPE_NAME = 'name';
    /**#@-*/

    /**
     * Get type of tax class key
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::KEY_TYPE);
    }

    /**
     * Get value of tax class key
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::KEY_VALUE);
    }
}
