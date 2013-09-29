<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports invitation report collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource\Report\Invitation;

class Collection
    extends \Magento\Invitation\Model\Resource\Invitation\Collection
{
    /**
     * Joins Invitation report data, and filter by date
     *
     * @param \Zend_Date|string $fromDate
     * @param \Zend_Date|string $toDate
     * @return \Magento\Invitation\Model\Resource\Report\Invitation\Collection
     */
    public function setDateRange($fromDate, $toDate)
    {
        $this->_reset();

        $canceledField = $this->getConnection()->getCheckSql(
            'main_table.status = '
                . $this->getConnection()->quote(\Magento\Invitation\Model\Invitation::STATUS_CANCELED),
            '1', '0'
        );

        $canceledRate = $this->getConnection()->getCheckSql(
            'COUNT(main_table.invitation_id) = 0',
            '0',
            'SUM(' . $canceledField . ') / COUNT(main_table.invitation_id) * 100'
        );

        $acceptedRate = $this->getConnection()->getCheckSql(
            'COUNT(main_table.invitation_id) = 0',
            '0',
            'COUNT(DISTINCT main_table.referral_id) / COUNT(main_table.invitation_id) * 100'
        );

        $this->addFieldToFilter('invitation_date', array('from' => $fromDate, 'to' => $toDate, 'time' => true))
            ->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(array(
                'sent' => new \Zend_Db_Expr('COUNT(main_table.invitation_id)'),
                'accepted' => new \Zend_Db_Expr('COUNT(DISTINCT main_table.referral_id)'),
                'canceled' => new \Zend_Db_Expr('SUM(' . $canceledField . ') '),
                'canceled_rate' => $canceledRate,
                'accepted_rate' => $acceptedRate
            ));

        $this->_joinFields($fromDate, $toDate);

        return $this;
    }

    /**
     * Join custom fields
     *
     * @return \Magento\Invitation\Model\Resource\Report\Invitation\Collection
     */
    protected function _joinFields()
    {
        return $this;
    }

    /**
     * Filters report by stores
     *
     * @param array $storeIds
     * @return \Magento\Invitation\Model\Resource\Report\Invitation\Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->addFieldToFilter('main_table.store_id', array('in' => (array)$storeIds));
        }
        return $this;
    }
}
