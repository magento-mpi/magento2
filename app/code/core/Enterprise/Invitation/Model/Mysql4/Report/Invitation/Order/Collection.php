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
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reports invitation order report collection
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Mysql4_Report_Invitation_Order_Collection
    extends Enterprise_Invitation_Model_Mysql4_Report_Invitation_Collection
{
    /**
     * Join custom fields
     *
     * @return Enterprise_Invitation_Model_Mysql4_Report_Invitation_Order_Collection
     */
    protected function _joinFields()
    {
        $this->getSelect()
            ->from('', array('sent' => new Zend_Db_Expr('COUNT(main_table.invitation_id)')))
            ->from('', array('accepted' => new Zend_Db_Expr('SUM(IF(main_table.status = "accepted", 1, 0))')))
            ->from('', array('canceled' => new Zend_Db_Expr('SUM(IF(main_table.status = "canceled", 1, 0))')));

        return $this;
    }

    /**
     * Additional data manipulation after collection was loaded
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->getItems() as $item) {
            $item->setCanceledRate($item->getCanceled() / $item->getSent() * 100);
            $item->setAcceptedRate($item->getAccepted() / $item->getSent() * 100);
            $item->setPurchased($this->_getPurchaseNumber(clone $this->getSelect()));
            $item->setPurchasedRate($item->getPurchased() / $item->getAccepted() * 100);
        }

        return $this;
    }

    /**
     * Calculate number of purchase from invited customers
     *
     * @param Zend_Db_Select $select
     * @return bool|int
     */
    protected function _getPurchaseNumber($select)
    {
        /* var $select Zend_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS)
            ->joinRight(array('o' => $this->getTable('sales/order')),
                'o.customer_id = main_table.referral_id AND o.store_id = main_table.store_id',
                array('COUNT(DISTINCT main_table.invitation_id)'));

       return $this->getConnection()->fetchOne($select);
    }
}
