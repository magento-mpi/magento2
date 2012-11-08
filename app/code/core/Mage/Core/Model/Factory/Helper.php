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
 * Helper factory model. Used to get helper objects
 */
class Mage_Core_Model_Factory_Helper
{
    /**
     * Get helper object
     *
     * @param  string $className
     * @return Mage_Core_Helper_Abstract
     */
    public function get($className)
    {
        return Mage::helper($className);
    }
}
