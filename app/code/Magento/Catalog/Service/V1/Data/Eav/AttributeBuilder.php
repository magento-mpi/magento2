<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Framework\Service\Data\AbstractObjectBuilder;

class AttributeBuilder extends AbstractObjectBuilder
{
    /**
     * Set attribute ID
     *
     * @param int $attributeId
     * @return $this
     */
    public function setId($attributeId)
    {
        return $this->_set(Attribute::ID, $attributeId);
    }

    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(Attribute::CODE, $code);
    }

    /**
     * Set attribute frontend label
     *
     * @param string|null $frontendLabel
     * @return $this
     */
    public function setFrontendLabel($frontendLabel)
    {
        return $this->_set(Attribute::LABEL, $frontendLabel);
    }

    /**
     * Set attribute default value
     *
     * @param string|null $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        return $this->_set(Attribute::DEFAULT_VALUE, $defaultValue);
    }

    /**
     * Set attribute is_required flag
     *
     * @param boolean $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired)
    {
        return $this->_set(Attribute::IS_REQUIRED, $isRequired);
    }

    /**
     * Set attribute is_user_defined flag
     *
     * @param boolean $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(Attribute::IS_USER_DEFINED, $isUserDefined);
    }
}
