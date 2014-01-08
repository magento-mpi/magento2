<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\Entity\V1;

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
}
