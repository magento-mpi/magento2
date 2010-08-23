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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer log resource
 *
 * @category   Mage
 * @package    Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Log_Model_Resource_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Visitor info data table
     *
     * @var string
     */
    protected $_visitorInfoTable;

    /**
     * Customer data table
     *
     * @var string
     */
    protected $_customerTable;

    /**
     * Url info data table
     *
     * @var string
     */
    protected $_urlInfoTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Log quote data table name.
     *
     * @var string
     */
    protected $_quoteTable;

    protected function _construct()
    {
        $this->_init('log/customer', 'log_id');

        $this->_visitorTable    = $this->getTable('log/visitor');
        $this->_visitorInfoTable= $this->getTable('log/visitor_info');
        $this->_urlTable        = $this->getTable('log/url_table');
        $this->_urlInfoTable    = $this->getTable('log/url_info_table');
        $this->_customerTable   = $this->getTable('log/customer');
        $this->_quoteTable      = $this->getTable('log/quote_table');
    }

    /**
     * Load an object
     *
     * @param  Mage_Core_Model_Abstract $object
     * @param  mixed $id
     * @param  string $field field to load by (defaults to model id)
     * @return Mage_Core_Model_Abstract
     */
    public function load(Mage_Core_Model_Abstract $object, $id, $field = null)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(array('ct' => $this->getMainTable()), array('login_at', 'logout_at') )
            ->joinInner(
                array('vt' => $this->_visitorTable),
                'vt.visitor_id=ct.visitor_id',
                array('last_visit_at'))
            ->joinInner(
                array('vit' => $this->_visitorInfoTable),
                'vt.visitor_id=vit.visitor_id',
                array('http_referer', 'remote_addr'))
            ->joinInner(
                array('uit' => $this->_urlInfoTable),
                'uit.url_id=vt.last_url_id',
                array('url'))
            ->where('ct.customer_id = :customer_id')
            ->order('ct.login_at desc')
            ->limit(1);

        $binds = array('customer_id' => $customerId);

        $object->setData($adapter->fetchRow($select, $binds));
        return $object;
    }
}
