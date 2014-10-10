<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api\Data\Entity\Attribute;

interface ValidationRuleInterface
{
    /**
     * Get validation rule name
     *
     * @return string
     */
    public function getName();

    /**
     * Get validation rule value
     *
     * @return string
     */
    public function getValue();
}
