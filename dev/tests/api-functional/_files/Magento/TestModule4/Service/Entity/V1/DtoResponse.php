<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\Entity\V1;


class DtoResponse extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return int
     */
    public function getEntityId()
    {
        return $this->_get('entity_id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_get('name');
    }

    /**
     * @param int $entityId
     *
     * @return DtoResponse
     */
    public function setEntityId($entityId)
    {
        return $this->_set('entity_id', $entityId);
    }

    /**
     * @param string $name
     *
     * @return DtoResponse
     */
    public function setName($name)
    {
        return $this->_set('name', $name);
    }
}