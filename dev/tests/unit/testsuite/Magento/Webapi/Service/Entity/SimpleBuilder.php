<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Service\Entity;

use Magento\Framework\Api\ExtensibleObjectBuilder;

class SimpleBuilder extends ExtensibleObjectBuilder
{
    /**
     * @param int $id
     * @return $this
     */
    public function setEntityId($id)
    {
        $this->data['entityId'] = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }
}
