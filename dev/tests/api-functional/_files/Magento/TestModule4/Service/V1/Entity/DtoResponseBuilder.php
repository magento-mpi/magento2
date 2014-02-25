<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1\Entity;

class DtoResponseBuilder extends \Magento\Service\Entity\AbstractObjectBuilder
{
    /**
     * @param int $entityId
     * @return DtoResponseBuilder
     */
    public function setEntityId($entityId)
    {
        return $this->_set('entity_id', $entityId);
    }

    /**
     * @param string $name
     * @return DtoResponseBuilder
     */
    public function setName($name)
    {
        return $this->_set('name', $name);
    }
}
