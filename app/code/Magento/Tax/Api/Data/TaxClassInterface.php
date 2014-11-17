<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * will be implemented by \Magento\Tax\Model\ClassModel
 * @see \Magento\Tax\Service\V1\Data\TaxClass
 */
interface TaxClassInterface
{
    /**#@+
     *
     * Tax class field key.
     */
    const KEY_ID = 'class_id';
    const KEY_NAME = 'class_name';
    const KEY_TYPE = 'class_type';
    /**#@-*/

    /**
     * Get tax class ID.
     *
     * @return int|null
     */
    public function getClassId();

    /**
     * Get tax class name.
     *
     * @return string
     */
    public function getClassName();

    /**
     * Get tax class type.
     *
     * @return string
     */
    public function getClassType();
}