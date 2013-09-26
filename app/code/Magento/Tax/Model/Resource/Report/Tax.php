<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax report resource model
 */
class Magento_Tax_Model_Resource_Report_Tax extends Magento_Reports_Model_Resource_Report_Abstract
{
    /**
     * @var Magento_Tax_Model_Resource_Report_Tax_CreatedatFactory
     */
    protected $_createdAtFactory;

    /**
     * @var Magento_Tax_Model_Resource_Report_Tax_UpdatedatFactory
     */
    protected $_updatedAtFactory;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Tax_Model_Resource_Report_Tax_CreatedatFactory $createdAtFactory
     * @param Magento_Tax_Model_Resource_Report_Tax_UpdatedatFactory $updatedAtFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Resource $resource,
        Magento_Tax_Model_Resource_Report_Tax_CreatedatFactory $createdAtFactory,
        Magento_Tax_Model_Resource_Report_Tax_UpdatedatFactory $updatedAtFactory
    ) {
        $this->_createdAtFactory = $createdAtFactory;
        $this->_updatedAtFactory = $updatedAtFactory;
        parent::__construct($logger, $resource);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Tax_Model_Resource_Report_Tax
     */
    public function aggregate($from = null, $to = null)
    {
        /** @var $createdAt Magento_Tax_Model_Resource_Report_Tax_Createdat */
        $createdAt = $this->_createdAtFactory->create();
        /** @var $updatedAt Magento_Tax_Model_Resource_Report_Tax_Updatedat */
        $updatedAt = $this->_updatedAtFactory->create();

        $createdAt->aggregate($from, $to);
        $updatedAt->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_TAX_FLAG_CODE);

        return $this;
    }
}
