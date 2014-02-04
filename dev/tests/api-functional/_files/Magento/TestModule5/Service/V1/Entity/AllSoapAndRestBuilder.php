<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\V1\Entity;

use Magento\Service\Entity\AbstractDto;
use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Some DTO short description.
 *
 * DTO long
 * multi line description.
 */
class AllSoapAndRestBuilder extends AbstractDtoBuilder
{
    const ID = 'id';
    const NAME = 'name';
    const IS_ENABLED = 'isEnabled';
    const HAS_NAME = 'hasName';

    /**
     * @param int $id
     * @return AllSoapAndRestBuilder
     */
    public function setId($id)
    {
        return $this->_set(self::ID, $id);
    }

    /**
     * @param string $name
     * @return AllSoapAndRestBuilder
     */
    public function setName($name)
    {
        return $this->_set(self::NAME, $name);
    }

    /**
     * Set flag if entity is enabled
     *
     * @param bool $isEnabled
     * @return AllSoapAndRestBuilder
     */
    public function setIsEnabled($isEnabled)
    {
        return $this->_set(self::IS_ENABLED, $isEnabled);
    }

    /**
     * Set flag if entity has name
     *
     * @param bool $hasName
     * @return AllSoapAndRestBuilder
     */
    public function setHasName($hasName)
    {
        return $this->_set(self::HAS_NAME, $hasName);
    }
}
