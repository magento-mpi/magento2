<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Helper class for stores.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Store extends Mage_PHPUnit_Helper_Abstract
{
    /**
     * Sets config data for store.
     * Can be needed to get your value in Mage::getStoreConfig()
     *
     * @param string $path
     * @param string $value
     * @param int|null|Mage_Core_Model_Store $store Non null value will work only if Magento is installed
     */
    public function setStoreConfig($path, $value, $store = null)
    {
        //needed to allow set arrays and objects to the config (initializes cache array).
        Mage::app()->getStore($store)->getConfig($path);
        Mage::app()->getStore($store)->setConfig($path, $value);
    }
}
