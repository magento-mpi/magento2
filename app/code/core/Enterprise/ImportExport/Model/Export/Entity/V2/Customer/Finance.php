<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export customer finance entity model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method      array getData() getData()
 */
class Enterprise_ImportExport_Model_Export_Entity_V2_Customer_Finance
    extends Mage_ImportExport_Model_Export_Entity_V2_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL   = '_email';
    const COLUMN_WEBSITE = '_website';
    const COLUMN_FINANCE_WEBSITE = '_finance_website';
    /**#@-*/

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_EMAIL, self::COLUMN_WEBSITE, self::COLUMN_FINANCE_WEBSITE);

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initWebsites(true);
        $this->setFileName($this->getEntityTypeCode());
    }

    /**
     * Get llist of permanent attributes
     *
     * @return array
     */
    public function getPermanentAttributes()
    {
        return $this->_permanentAttributes;
    }

    /**
     * Export process
     *
     * @return string
     */
    public function export()
    {
        /** @var $customerCollection Enterprise_ImportExport_Model_Resource_Customer_Collection */
        $customerCollection = Mage::getResourceModel('Enterprise_ImportExport_Model_Resource_Customer_Collection');

        /** @var $exportCustomer Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer */
        $exportCustomer = Mage::getModel('Mage_ImportExport_Model_Export_Entity_V2_Eav_Customer');
        $exportCustomer->setParameters($this->_parameters);
        $customerCollection = $exportCustomer->filterEntityCollection($customerCollection);

        // join with finance data tables
        /** @var $importExportHelper Enterprise_ImportExport_Helper_Data */
        $importExportHelper = Mage::helper('Enterprise_ImportExport_Helper_Data');

        if ($importExportHelper->isRewardPointsEnabled()) {
            $customerCollection->joinWithRewardPoints();
        }

        if ($importExportHelper->isCustomerBalanceEnabled()) {
            $customerCollection->joinWithCustomerBalance();
        }

        $permanentAttributes = $this->getPermanentAttributes();
        $validAttributeCodes = $this->_getEntityAttributes();
        $writer              = $this->getWriter();

        // create export file
        $writer->setHeaderCols(array_merge($permanentAttributes, $validAttributeCodes));
        /** @var $customer Mage_Customer_Model_Customer */
        foreach ($customerCollection as $customer) { // go through all customers
            /** @var $website Mage_Core_Model_Website */
            foreach (Mage::app()->getWebsites() as $website) {
                $row = array();
                foreach ($validAttributeCodes as $code) {
                    $websiteCode = $website->getCode() . '_' . $code;
                    $websiteData = $customer->getData($websiteCode);
                    if (null !== $websiteData) {
                        $row[$code] = $websiteData;
                    }
                }

                if (!empty($row)) {
                    $row[self::COLUMN_EMAIL] = $customer->getEmail();
                    $row[self::COLUMN_WEBSITE] = $this->_websiteIdToCode[$customer->getWebsiteId()];
                    $row[self::COLUMN_FINANCE_WEBSITE] = $website->getCode();
                    $writer->writeRow($row);
                }
            }
        }
        return $writer->getContents();
    }

    /**
     * Entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_finance';
    }

    /**
     * Entity attributes collection getter
     *
     * @return Varien_Data_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection');
    }

    /**
     * Get attributes for export
     *
     * @return array
     */
    protected function _getEntityAttributes()
    {
        $attributes = array();
        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attributes[] = $attribute->getAttributeCode();
        }
        return $attributes;
    }
}
