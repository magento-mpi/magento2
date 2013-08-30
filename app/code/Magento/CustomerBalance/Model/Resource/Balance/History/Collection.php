<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Balance history collection
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerBalance_Model_Resource_Balance_History_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento_CustomerBalance_Model_Balance_History',
            'Magento_CustomerBalance_Model_Resource_Balance_History'
        );
    }

    /**
     * Instantiate select joined to balance
     *
     * @return Magento_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinInner(array('b' => $this->getTable('magento_customerbalance')),
                'main_table.balance_id = b.balance_id', array('customer_id'         => 'b.customer_id',
                                                              'website_id'          => 'b.website_id',
                                                              'base_currency_code'  => 'b.base_currency_code'))
        ;
        return $this;
    }

    /**
     * Filter collection by specified websites
     *
     * @param array|int $websiteIds
     * @return Magento_CustomerBalance_Model_Resource_Balance_History_Collection
     */
    public function addWebsitesFilter($websiteIds)
    {
        $this->getSelect()->where('b.website_id IN (?)', $websiteIds);
        return $this;
    }

    /**
     * Retrieve history data
     *
     * @param  string $customerId
     * @param string|null $websiteId
     * @return Magento_CustomerBalance_Model_Resource_Balance_History_Collection
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
