<?php

/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Unit testing helper for Magento Helper objects.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Model_Helper extends Mage_PHPUnit_Helper_Model_Abstract
{
    /**
     * Returns real Magento helper's class name by helper's name.
     *
     * @param string $modelName Helper name like 'catalog'
     * @return string
     */
    public function getRealModelClass($modelName)
    {
        return Mage::getConfig()->getHelperClassName($modelName);
    }
}
