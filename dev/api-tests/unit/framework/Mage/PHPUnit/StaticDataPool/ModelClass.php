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
