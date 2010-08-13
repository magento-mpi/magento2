<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource helper class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Retrieve connection for read data
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getReadAdapter()
    {
        return $this->_getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getWriteAdapter()
    {
        return $this->_getConnection('write');
    }

    /**
     * Create connection to resource
     *
     * @param string $name
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getConnection($name)
    {
        $resource   = Mage::getSingleton('core/resource');
        /* @see Mage_Core_Model_Resource */
        $connection = $resource->getConnection($name);

        return $connection;
    }
}
