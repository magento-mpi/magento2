<?php
/**
 * Customer segment report detail collection
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_CustomerSegment_Model_Resource_Segment_Report_Detail_Collection
    extends Magento_CustomerSegment_Model_Resource_Report_Customer_Collection
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $registryManager
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $registryManager,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_registryManager = $registryManager;
        parent::__construct($fetchStrategy, $coreConfig);
    }

    /**
     * @return Magento_CustomerSegment_Model_Resource_Segment_Report_Detail_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addNameToSelect()
            ->setViewMode($this->getCustomerSegment()->getViewMode())
            ->addSegmentFilter($this->getCustomerSegment())
            ->addWebsiteFilter($this->_registryManager->registry('filter_website_ids'))
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerSegment()
    {
        return $this->_registryManager->registry('current_customer_segment');
    }
}
