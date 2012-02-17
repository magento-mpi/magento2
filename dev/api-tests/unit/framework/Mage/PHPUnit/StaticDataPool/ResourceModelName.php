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
 * Class for pool of real resource model names
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_ResourceModelName extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Real resource models names.
     *  array('catalog/product' => 'mysql4_catalog/product')
     *
     * @var array
     */
    protected $_realResourceModels = array();


    /**
     * Returns real model's class name.
     *
     * @param string $model
     * @return string
     */
    public function getResourceModelName($model)
    {
        if (!isset($this->_realResourceModels[$model])) {
            return false;
        }
        return $this->_realResourceModels[$model];
    }

    /**
     * Set real resource model's name.
     *
     * @param string $model
     * @param string $realName
     */
    public function setResourceModelName($model, $realName)
    {
        $this->_realResourceModels[$model] = $realName;
    }
}
