<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

use Magento\Framework\Service\Data\AbstractObject;

class Attribute extends AbstractObject
{
    const ID = 'id';
    const CODE = 'code';
    const IS_REQUIRED = 'is_required';
    const IS_USER_DEFINED = 'is_user_defined';
    const LABEL = 'frontend_label';
    const DEFAULT_VALUE = 'default_value';

    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Get attribute frontend label
     *
     * @return string|null
     */
    public function getFrontendLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * Get attribute default value
     *
     * @return string|null
     */
    public function getDefaultValue()
    {
        return $this->_get(self::DEFAULT_VALUE);
    }

    /**
     * Get attribute is_required flag
     *
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->_get(self::IS_REQUIRED);
    }

    /**
     * Get attribute is_user_defined flag
     *
     * @return boolean
     */
    public function getIsUserDefined()
    {
        return $this->_get(self::IS_USER_DEFINED);
    }
}
