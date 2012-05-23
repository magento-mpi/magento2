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
 * Abstract class for "model" helpers.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_Helper_Model_Abstract extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Returns real model's class name by model's name.
     *
     * @param string $modelName
     * @return string
     */
    abstract public function getRealModelClass($modelName);
}
