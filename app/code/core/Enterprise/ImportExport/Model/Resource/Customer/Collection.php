<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customized customers collection
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Resource_Customer_Collection
    extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * Additional filters to use
     *
     * @var array
     */
    protected $_usedFiltersNotNull = array();

    /**
     * Join with reward points table
     *
     * @return Enterprise_ImportExport_Model_Resource_Customer_Collection
     */
    public function joinWithRewardPoints()
    {
        /** @var $rewardResourceModel Enterprise_Reward_Model_Resource_Reward */
        $rewardResourceModel = Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward');

        $joinFlag = 'join_reward_points';

        if (!$this->getFlag($joinFlag)) {
            $this->joinTable(
                $rewardResourceModel->getMainTable(),
                'customer_id = entity_id',
                array(
                    Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS
                    => 'points_balance'
                ),
                null,
                'left'
            );
            $this->setFlag($joinFlag, true);

            $this->_usedFiltersNotNull[] = $rewardResourceModel->getMainTable() . '.points_balance';
        }

        return $this;
    }

    /**
     * Join with store credit table
     *
     * @return Enterprise_ImportExport_Model_Resource_Customer_Collection
     */
    public function joinWithCustomerBalance()
    {
        /** @var $customerBalanceResourceModel Enterprise_CustomerBalance_Model_Resource_Balance */
        $customerBalanceResourceModel = Mage::getResourceModel('Enterprise_CustomerBalance_Model_Resource_Balance');

        $joinFlag = 'join_customer_balance';

        if (!$this->getFlag($joinFlag)) {
            $this->joinTable(
                $customerBalanceResourceModel->getMainTable(),
                'customer_id = entity_id',
                array(
                    Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
                        ::COLUMN_CUSTOMER_BALANCE
                    => 'amount'
                ),
                null,
                'left'
            );
            $this->setFlag($joinFlag, true);

            $this->_usedFiltersNotNull[] = $customerBalanceResourceModel->getMainTable() . '.amount';
        }

        return $this;
    }

    /**
     * Additional filters
     *
     * @return Enterprise_ImportExport_Model_Resource_Customer_Collection
     */
    protected function _beforeLoad()
    {
        if ($this->_usedFiltersNotNull) {
            $filterArray = array();
            foreach ($this->_usedFiltersNotNull as $filter) {
                $filterArray[] = $this->getSelect()
                    ->getAdapter()
                    ->prepareSqlCondition($filter, array('notnull' => true));
            }
            $conditionStr = implode(' OR ', $filterArray);
            $this->getSelect()->where($conditionStr);
        }

        return parent::_beforeLoad();
    }
}
