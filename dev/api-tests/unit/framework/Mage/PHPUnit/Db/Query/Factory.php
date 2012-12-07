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
