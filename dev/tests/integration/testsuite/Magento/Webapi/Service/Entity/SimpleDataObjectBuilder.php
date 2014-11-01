<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class SimpleDataObjectBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * @param int $entityId
     */
    public function setEntityId($entityId)
    {
        $this->_data['entityId'] = $entityId;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
    }
}
