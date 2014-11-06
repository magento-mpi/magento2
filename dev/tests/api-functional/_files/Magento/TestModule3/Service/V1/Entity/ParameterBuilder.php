<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule3\Service\V1\Entity;

class ParameterBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set Name.
     *
     * @param string $name
     * @return \Magento\TestModule3\Service\V1\Entity\ParameterBuilder
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Set value.
     *
     * @param string $value
     * @return \Magento\TestModule3\Service\V1\Entity\ParameterBuilder
     */
    public function setValue($value)
    {
        $this->data['value'] = $value;
        return $this;
    }
}
