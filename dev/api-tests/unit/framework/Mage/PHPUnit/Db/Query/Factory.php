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
 * Local DB queries factory
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Query_Factory
{
    /**
     * Array, which contains available processor class names
     *
     * @var array
     */
    protected static $_queryModels = array(
        'Mage_PHPUnit_Db_Query_Select',
        'Mage_PHPUnit_Db_Query_Delete'
    );

    /**
     * Gets all available query processors
     *
     * @return array
     */
    public static function getAllQueryModels()
    {
        $models = array();
        foreach (self::$_queryModels as $modelName) {
            $models[$modelName] = new $modelName();
        }
        return $models;
    }
}
