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
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_PHPUnit_Db_Type_Local extends Mage_Core_Model_Resource_Type_Db
{
    /**
     * Get stub adapter
     *
     * @param array $config Connection config
     * @return Mage_PHPUnit_Db_Adapter
     */
    public function getConnection($config)
    {
        $configArr = (array)$config;
        $configArr['profiler'] = false;

        return $this->_getDbAdapterInstance($configArr);
    }

    /**
     * Create and return stub adapter object instance
     *
     * @param array $configArr Connection config
     * @return Mage_PHPUnit_Db_Adapter
     */
    protected function _getDbAdapterInstance($configArr)
    {
        $className = $this->_getDbAdapterClassName();
        $adapter = new $className($configArr);
        return $adapter;
    }

    /**
     * Retrieve stub adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Mage_PHPUnit_Db_Adapter';
    }

}
