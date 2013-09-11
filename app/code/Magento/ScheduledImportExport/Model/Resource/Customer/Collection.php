<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customized customers collection
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Resource\Customer;

class Collection
    extends \Magento\Customer\Model\Resource\Customer\Collection
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
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
     */
    public function joinWithRewardPoints()
    {
        /** @var $rewardResourceModel \Magento\Reward\Model\Resource\Reward */
        $rewardResourceModel = \Mage::getResourceModel('Magento\Reward\Model\Resource\Reward');

        $joinFlag = 'join_reward_points';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website \Magento\Core\Model\Website */
            foreach (\Mage::app()->getWebsites() as $website) {
                $tableName  = $rewardResourceModel->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.points_balance';
                $fieldAlias = $website->getCode() . '_'
                    . \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
                    ::COLUMN_REWARD_POINTS;

                $this->joinTable(
                    array($tableAlias => $tableName),
                    'customer_id = entity_id',
                    array($fieldAlias => $fieldName),
                    array('website_id' => $website->getId()),
                    'left'
                );

                $this->_usedFiltersNotNull[] = $fieldName;
            }
            $this->setFlag($joinFlag, true);
        }

        return $this;
    }

    /**
     * Join with store credit table
     *
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
     */
    public function joinWithCustomerBalance()
    {
        /** @var $customerBalanceResourceModel \Magento\CustomerBalance\Model\Resource\Balance */
        $customerBalanceResourceModel = \Mage::getResourceModel('Magento\CustomerBalance\Model\Resource\Balance');

        $joinFlag = 'join_customer_balance';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website \Magento\Core\Model\Website */
            foreach (\Mage::app()->getWebsites() as $website) {
                $tableName  = $customerBalanceResourceModel->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.amount';
                $fieldAlias = $website->getCode() . '_'
                    . \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
                    ::COLUMN_CUSTOMER_BALANCE;

                $this->joinTable(
                    array($tableAlias => $tableName),
                    'customer_id = entity_id',
                    array($fieldAlias => $fieldName),
                    array('website_id' => $website->getId()),
                    'left'
                );

                $this->_usedFiltersNotNull[] = $fieldName;
            }
            $this->setFlag($joinFlag, true);
        }

        return $this;
    }

    /**
     * Additional filters
     *
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
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
