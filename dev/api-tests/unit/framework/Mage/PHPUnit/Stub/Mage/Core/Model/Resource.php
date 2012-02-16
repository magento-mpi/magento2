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
 * Stub class for Mage_Core_Model_Resource.
 * Needed to load real modules configs.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Mage_Core_Model_Resource extends Mage_Core_Model_Resource
{
    /**
     * Sets connection object.
     * It is needed to mock connection adapter object.
     *
     * @param string $connectionName
     * @param Zend_Db_Adapter_Abstract $connectionObject
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config
     */
    public function setConnection($connectionName, $connectionObject)
    {
        $this->_connections[$connectionName] = $connectionObject;
        return $this;
    }
}
