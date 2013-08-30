<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper factory model. Used to get helper objects
 */
class Magento_Core_Model_Factory_Helper
{
    /**
     * Get helper object
     *
     * @param  string $className
     * @return Magento_Core_Helper_Abstract
     */
    public function get($className)
    {
        return Mage::helper($className);
    }
}
