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
 * @category    Mage
 * @package     Mage_Oscommerce
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Resource_Oscommerce_Order extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce_order', 'osc_magento_id');
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getProducts()
    {
        $order = Mage::registry('current_oscommerce_order');
        $result = array();
        if ($order && $order->getData() && $id = $order->getId()) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('oscommerce/oscommerce_order_products'))
                ->where('osc_magento_id = :order_id');
            $result = $this->_getReadAdapter()->fetchAll($select, array(':order_id' => (int)$id));
        }
        return $result;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getTotal()
    {
        $order = Mage::registry('current_oscommerce_order');
        $result = array();
        if ($order && $order->getData() && $id = $order->getId()) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('oscommerce/oscommerce_order_total'))
                ->where('osc_magento_id = :order_id')
                ->order('sort_order');
            $result = $this->_getReadAdapter()->fetchAll($select, array(':order_id' => (int)$id));
        }
        return $result;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getComments()
    {
        $order = Mage::registry('current_oscommerce_order');
        $result = array();
        if ($order && $order->getData() && $id = $order->getId()) {
            $select = $this->_getReadAdapter()->select();
            $select->from($this->getTable('oscommerce/oscommerce_order_history'))
                ->where('osc_magento_id = :order_id');
            $result = $this->_getReadAdapter()->fetchAll($select, array(':order_id' => (int)$id));
        }
        return $result;
    }
}
