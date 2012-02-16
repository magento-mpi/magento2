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
 * Simple class for pool of data.
 * Key-value data.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPool_Simple extends Mage_PHPUnit_StaticDataPool_Abstract
{
    /**
     * Key-value data array
     *
     * @var array array(key => value)
     */
    protected $_data = array();

    /**
     * Value getter by key.
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : false;
    }

    /**
     * Value setter for key
     *
     * @param string $key
     * @param mixed $value
     * @return Mage_PHPUnit_StaticDataPool_Simple
     */
    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
}
