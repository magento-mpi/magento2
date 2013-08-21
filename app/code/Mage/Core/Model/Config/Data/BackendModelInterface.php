<?php
/**
 * Configuration value backend model interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Config_Data_BackendModelInterface
{
    /**
     * Process config value
     *
     * @param string $value
     * @return mixed
     */
    public function processValue($value);
}
