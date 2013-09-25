<?php
/**
 * Class, which always requires validation for the xml-files.
 * So our tests do not depend on whether developer mode is on.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Modular_Magento_Sales_ValidationStateOn implements Magento_Config_ValidationStateInterface
{
    /**
     * Retrieve current validation state
     *
     * @return boolean
     */
    public function isValidated()
    {
        return true;
    }
}
