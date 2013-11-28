<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Service\Entity\AbstractDto;

/**
 * Class SimpleDto
 *
 * @package Magento\Webapi\Service\Entity
 */
class SimpleDto extends AbstractDto
{
    /**
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->_getData('entity_id');
    }

    /**
     * @param $entityId
     *
     * @return SimpleDto
     */
    public function setEntityId($entityId)
    {
        return $this->_setData('entity_id', $entityId);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * @param string $name
     *
     * @return SimpleDto
     */
    public function setName($name)
    {
        return $this->_setData('name', $name);
    }
}
