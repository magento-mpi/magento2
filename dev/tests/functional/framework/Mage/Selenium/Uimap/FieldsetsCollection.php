<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UIMap Fieldsets collection class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_Uimap_FieldsetsCollection extends ArrayObject
{
    /**
     * Get Fieldset structure by name
     *
     * @param string $name Fieldset name
     *
     * @return Mage_Selenium_Uimap_Fieldset|null
     */
    public function getFieldset($name)
    {
        return isset($this[$name]) ? $this[$name] : null;
    }
}