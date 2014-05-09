<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UIMap Tabs collection class
 *
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        return isset($this[$name]) ? $this[$name] : null;
    }
}