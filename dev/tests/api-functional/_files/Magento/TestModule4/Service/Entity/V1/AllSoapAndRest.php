<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\Entity\V1;


use Magento\Service\Entity\AbstractDto;

class AllSoapAndRest extends AbstractDto
{
    const ID = 'id';
    const NAME = 'name';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * @param int $id
     * @return AllSoapAndRest
     */
    public function setId($id)
    {
        return $this->_set(self::ID, $id);
    }

    /**
     * @param string $name
     * @return AllSoapAndRest
     */
    public function setName($name)
    {
        return $this->_set(self::NAME, $name);
    }

} 