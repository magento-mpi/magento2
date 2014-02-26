<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service\V1\Entity;

class DtoRequestBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{

    /**
     * @param string $name
     * @return DtoRequest
     */
    public function setName($name)
    {
        return $this->_set('name', $name);
    }

    /**
     * @param int $entityId
     * @return DtoRequest
     */
    public function setEntityId($entityId)
    {
        return $this->_set('entity_id', $entityId);
    }

}
