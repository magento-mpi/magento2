<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\Entity\V1;

use Magento\Service\Entity\AbstractDto;

/**
 * Some DTO short description.
 *
 * DTO long
 * multi line description.
 */
class AllSoapAndRest extends AbstractDto
{
    const ID = 'id';
    const NAME = 'name';
    const IS_ENABLED = 'isEnabled';
    const HAS_NAME = 'hasName';

    /**
     * Retrieve item ID.
     *
     * @return int Item ID
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Retrieve item Name.
     *
     * @return string|null Item name
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Check if entity is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_get(self::IS_ENABLED);
    }

    /**
     * Check if current entity has name defined
     *
     * @return bool
     */
    public function hasName()
    {
        return $this->_get(self::HAS_NAME);
    }
}
