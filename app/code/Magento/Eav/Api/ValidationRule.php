<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Api;

interface ValidationRule
{
    /**
     * Constants used as keys into $_data
     */
    const NAME = 'name';

    const VALUE = 'value';

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
