<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

class SimpleBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * @param int $entityId
     */
    public function setEntityId($entityId)
    {
        $this->data['entityId'] = $entityId;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
    }
}
