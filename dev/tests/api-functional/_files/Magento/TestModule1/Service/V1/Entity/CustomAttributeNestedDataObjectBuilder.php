<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\V1\Entity;

class CustomAttributeNestedDataObjectBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }
}
