<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports invitation order report collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource\Report\Invitation\Order;

class Collection extends \Magento\Invitation\Model\Resource\Report\Invitation\Collection
{
    /**
     * Join custom fields
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $acceptedExpr = 'SUM(' . $this->getConnection()->getCheckSql(
            'main_table.status = ' . $this->getConnection()->quote(
                \Magento\Invitation\Model\Invitation\Status::STATUS_ACCEPTED
            ) . 'AND main_table.referral_id IS NOT NULL',
            '1',
            '0'
        ) . ')';
        $canceledExpr = 'SUM(' . $this->getConnection()->getCheckSql(
            'main_table.status = ' . $this->getConnection()->quote(
                \Magento\Invitation\Model\Invitation\Status::STATUS_CANCELED
            ),
            '1',
            '0'
        ) . ')';

        $this->getSelect()->reset(
            \Zend_Db_Select::COLUMNS
        )->columns(
            array('sent' => new \Zend_Db_Expr('COUNT(main_table.invitation_id)'))
        )->columns(
            array('accepted' => new \Zend_Db_Expr($acceptedExpr))
        )->columns(
            array('canceled' => new \Zend_Db_Expr($canceledExpr))
        );

        return $this;
    }

    /**
     * Additional data manipulation after collection was loaded
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->getItems() as $item) {
            if ($item->getSent()) {
                $item->setCanceledRate($item->getCanceled() / $item->getSent() * 100);
                $item->setAcceptedRate($item->getAccepted() / $item->getSent() * 100);
            } else {
                $item->setCanceledRate(0);
                $item->setAcceptedRate(0);
            }

            $item->setPurchased($this->_getPurchaseNumber(clone $this->getSelect()));

            if ($item->getAccepted()) {
                $item->setPurchasedRate($item->getPurchased() / $item->getAccepted() * 100);
            } else {
                $item->setPurchasedRate(0);
            }
        }

        return $this;
    }

    /**
     * Calculate number of purchase from invited customers
     *
     * @param \Zend_Db_Select $select
     * @return bool|int
     */
    protected function _getPurchaseNumber($select)
    {
        /* var $select \Zend_Db_Select */
        $select->reset(
            \Zend_Db_Select::COLUMNS
        )->joinRight(
            array('o' => $this->getTable('sales_order')),
            'o.customer_id = main_table.referral_id AND o.store_id = main_table.store_id',
            array('cnt' => 'COUNT(main_table.invitation_id)')
        );
        return $this->getConnection()->fetchOne($select);
    }
}
