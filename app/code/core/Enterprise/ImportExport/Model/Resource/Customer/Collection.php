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
class Enterprise_ImportExport_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * Join with reward points table
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function joinWithRewardPoints()
    {
        /** @var $rewardResourceModel Enterprise_Reward_Model_Resource_Reward */
        $rewardResourceModel = Mage::getResourceModel('Enterprise_Reward_Model_Resource_Reward');

        $this->joinTable(
            $rewardResourceModel->getMainTable(),
            'customer_id = entity_id',
            array(Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_REWARD_POINTS
                => 'points_balance')
        );

        return $this;
    }

    /**
     * Join with store credit table
     *
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function joinWithCustomerBalance()
    {
        /** @var $customerBalanceResourceModel Enterprise_CustomerBalance_Model_Resource_Balance */
        $customerBalanceResourceModel = Mage::getResourceModel('Enterprise_CustomerBalance_Model_Resource_Balance');

        $this->joinTable(
            $customerBalanceResourceModel->getMainTable(),
            'customer_id = entity_id',
            array(Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COL_CUSTOMER_BALANCE
                => 'amount')
        );

        return $this;
    }
}
