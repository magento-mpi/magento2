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
 * Class for pool of real model class names
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_ModelClass extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Real models class names.
     *  array('catalog/product' => 'Mage_Catalog_Model_Product')
     *  or for example:
     *  array('catalog/product' => 'Ford_Catalog_Model_Product')
     *
     * @var array
     */
    protected $_realModelClasses = array();


    /**
     * Returns real model's class name.
     *
     * @param string $model
     * @return string
     */
    public function getRealModelClass($model)
    {
        if (!isset($this->_realModelClasses[$model])) {
            return false;
        }
        return $this->_realModelClasses[$model];
    }

    /**
     * Set real model's class name.
     *
     * @param string $model
     * @param string $className
     */
    public function setRealModelClass($model, $className)
    {
        $this->_realModelClasses[$model] = $className;
    }
}
