<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * @codeCoverageIgnore
 */
class AttributeGroup extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array, make typos less likely
     */
    const KEY_ID = 'id';

    const KEY_NAME = 'name';
    /**#@-*/

    /**
     * Retrieve id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }
}
