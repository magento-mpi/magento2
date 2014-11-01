<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\V1\Entity;

class CustomAttributeNestedDataObjectBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_data['name'] = $name;
        return $this;
    }
}
