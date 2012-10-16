<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper registry model. Used to get helper objects
 */
class Mage_Core_Model_Helper_Registry
{
    /**
     * Get helper object
     *
     * @param  string $moduleName
     * @return Mage_Core_Helper_Abstract
     */
    public function get($moduleName)
    {
        return Mage::helper($moduleName);
    }
}
