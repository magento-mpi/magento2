<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule4\Service\V1\Entity;

class DataObjectRequestBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * @param string $name
     * @return DataObjectRequest
     */
    public function setName($name)
    {
        return $this->_set('name', $name);
    }

    /**
     * @param int $entityId
     * @return DataObjectRequest
     */
    public function setEntityId($entityId)
    {
        return $this->_set('entity_id', $entityId);
    }
}
