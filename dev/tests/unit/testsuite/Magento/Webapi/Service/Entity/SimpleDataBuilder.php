<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\Entity;

use Magento\Service\Data\AbstractObjectBuilder;

class SimpleDataBuilder extends AbstractObjectBuilder
{
    /**
     * @param int $id
     * @return $this
     */
    public function setEntityId($id)
    {
        $this->_data['entityId'] = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }
}
