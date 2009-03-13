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
 * @category   Enterprise
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Model_Mysql4_Balance extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance', 'primary_id');
    }

    public function loadByCustomerWebsite($object, $customerId, $websiteId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable())
            ->where($this->getReadConnection()->quoteInto('customer_id = ?', $customerId))
            ->where($this->getReadConnection()->quoteInto('website_id = ?', $websiteId));

        $data = $this->getReadConnection()->fetchRow($select);
        if( is_array($data) ) {
            $object->addData($data);
        }
        return $this;
    }

    public function getTotal($customerId, $websiteId=false)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('SUM(balance)'))
            ->where($this->getReadConnection()->quoteInto('customer_id = ?', $customerId));

        if( $websiteId ) {
            $select->where($this->getReadConnection()->quoteInto('website_id = ?', $websiteId));
        }

        $data = $this->getReadConnection()->fetchOne($select);

        return $data;
    }
}
