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
namespace Magento\Core\Model\Factory;

class Helper
{
    /**
     * Get helper object
     *
     * @param  string $className
     * @return \Magento\Core\Helper\AbstractHelper
     */
    public function get($className)
    {
        return \Mage::helper($className);
    }
}
