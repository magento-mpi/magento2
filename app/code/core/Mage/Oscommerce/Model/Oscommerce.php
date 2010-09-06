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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce resource model
 *
 * @method Mage_Oscommerce_Model_Resource_Oscommerce _getResource()
 * @method Mage_Oscommerce_Model_Resource_Oscommerce getResource()
 * @method string getName()
 * @method Mage_Oscommerce_Model_Oscommerce setName(string $value)
 * @method string getCreatedAt()
 * @method Mage_Oscommerce_Model_Oscommerce setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Mage_Oscommerce_Model_Oscommerce setUpdatedAt(string $value)
 * @method string getHost()
 * @method Mage_Oscommerce_Model_Oscommerce setHost(string $value)
 * @method int getPort()
 * @method Mage_Oscommerce_Model_Oscommerce setPort(int $value)
 * @method string getDbName()
 * @method Mage_Oscommerce_Model_Oscommerce setDbName(string $value)
 * @method string getDbUser()
 * @method Mage_Oscommerce_Model_Oscommerce setDbUser(string $value)
 * @method string getDbPassword()
 * @method Mage_Oscommerce_Model_Oscommerce setDbPassword(string $value)
 * @method string getDbType()
 * @method Mage_Oscommerce_Model_Oscommerce setDbType(string $value)
 * @method string getTablePrefix()
 * @method Mage_Oscommerce_Model_Oscommerce setTablePrefix(string $value)
 * @method int getSendSubscription()
 * @method Mage_Oscommerce_Model_Oscommerce setSendSubscription(int $value)
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Oscommerce extends Mage_Core_Model_Abstract
{
    const DEFAULT_PORT = 3360;
    const CONNECTION_TYPE = 'pdo_mysql';
    const CONNECTION_NAME = 'oscommerce_db';

    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        Mage::getSingleton('oscommerce/config')->initForeignConnection($this->getData());

//		if (Mage::app()->getRequest()->getActionName() == 'run') {
//			$this->importStores();
//		}

    }
    /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('oscommerce/session');
    }

    public function importStores() {
        $this->getResource()->importStores($this);
    }

    public function getImportTypeIdByCode($code = '') {
        return $this->getResource()->getImportTypeIdByCode($code);
    }

    public function loadOrders($customerId, $websiteId)
    {
        return $this->getResource()->loadOrders($customerId, $websiteId);
    }

    public function loadOrderById($id)
    {
        return $this->getResource()->loadOrderById($id);
    }

    public function deleteImportedRecords($id)
    {
        if (isset($id) && $id == $this->getId()) {
            $this->getResource()->deleteRecords($id);
        }
    }
}
