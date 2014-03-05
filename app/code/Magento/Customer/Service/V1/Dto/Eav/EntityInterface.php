<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

/**
 * Interface EntityInterface
 */
interface EntityInterface
{
    /**
     * Retrieve array of all attributes, in the form of 'attribute code' => <attribute value'
     *
     * @return string[] attributes, in the form of 'attribute code' => <attribute value'
     */
    public function getAttributes();

    /**
     * Get attribute value for given attribute code
     *
     * @param string $attributeCode
     * @return string|null
     */
    public function getAttribute($attributeCode);

}
