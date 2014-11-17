<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * @codeCoverageIgnore
 */
class ValidationRuleBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set validation rule name
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->_set(ValidationRule::NAME, $name);
    }

    /**
     * Set validation rule value
     *
     * @param  string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(ValidationRule::VALUE, $value);
    }
}
