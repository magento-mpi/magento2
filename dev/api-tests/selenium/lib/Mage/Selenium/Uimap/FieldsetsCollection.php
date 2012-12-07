<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UIMap Fieldsets collection class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
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