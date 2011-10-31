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
 * Helper factory class
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Helper_Factory
{
    /**
     * Helpers array
     *
     * @var array array('key' => object, ...)
     */
    protected static $_helpers = array();

    /**
     * Creates new helper object by keyname
     *
     * @param string $key helper's part of the classname after Mage_PHPUnit_Helper_
     * @return Mage_PHPUnit_Helper_Abstract
     */
    protected static function _initHelper($key)
    {
        $class = 'Mage_PHPUnit_Helper_' . uc_words($key);
        return new $class();
    }

    /**
     * Returns a helper object.
     *
     * @param string $key
     * @return Mage_PHPUnit_Helper_Abstract|null
     */
    public static function getHelper($key)
    {
        if (!array_key_exists($key, self::$_helpers[$key])) {
            self::$_helpers[$key] = self::_initHelper($key);
        }

        return self::$_helpers[$key];
    }
}
