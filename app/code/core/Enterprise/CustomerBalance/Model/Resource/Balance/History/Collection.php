<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Balance history collection
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerBalance_Model_Resource_Balance_History_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init(
            'enterprise_customerbalance/balance_history',
            'Enterprise_CustomerBalance_Model_Resource_Balance_History'
        );
    }

    /**
     * Instantiate select joined to balance
     *
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinInner(array('b' => $this->getTable('enterprise_customerbalance')),
                'main_table.balance_id = b.balance_id', array('customer_id'         => 'b.customer_id',
                                                              'website_id'          => 'b.website_id',
                                                              'base_currency_code'  => 'b.base_currency_code'))
        ;
        return $this;
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(
            is_array($websiteId) ? 'b.website_id IN (?)' : 'b.website_id = ?', $websiteId
        );
        return $this;
    }

    /**
     * Retrieve history data
     *
     * @param  string $customerId
     * @param string|null $websiteId
     * @return Enterprise_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    public function loadHistoryData($customerId, $websiteId = null)
    {
        $this->addFieldToFilter('customer_id', $customerId)
                ->addOrder('updated_at', 'DESC')
                ->addOrder('history_id', 'DESC');
        if (!empty($websiteId)) {
            $this->getSelect()->where('b.website_id IN (?)', $websiteId);
        }
        return $this;
    }
}
