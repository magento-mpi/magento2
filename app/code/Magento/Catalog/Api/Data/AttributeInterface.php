<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * Interface AttributeInterface must be implemented by \Magento\Catalog\Model\Entity\Attribute
 */
interface AttributeInterface
{
    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getId();
    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get attribute frontend label
     *
     * @return string|null
     */
    public function getFrontendLabel();

    /**
     * Get attribute default value
     *
     * @return string|null
     */
    public function getDefaultValue();
    /**
     * Get attribute is_required flag
     *
     * @return boolean
     */
    public function getIsRequired();

    /**
     * Get attribute is_user_defined flag
     *
     * @return boolean
     */
    public function getIsUserDefined();

    /**
     * Get frontend input type
     *
     * @return string
     */
    public function getFrontendInput();
}
