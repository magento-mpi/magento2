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
 * UIMap Tabs collection class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     {license_link}
 */
class Mage_Selenium_Uimap_TabsCollection extends ArrayObject
{
    /**
     * Get Tab structure by name
     *
     * @param string $name Tab name
     *
     * @return Mage_Selenium_Uimap_Tab|null
     */
    public function getTab($name)
    {
        return isset($this[$name])
                ? $this[$name]
                : null;
    }
}