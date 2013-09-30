<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export customer finance entity model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo refactor in the scope of https://wiki.magento.com/display/MAGE2/Technical+Debt+%28Team-Donetsk-B%29
 *
 * @method      array getData() getData()
 */
class Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance
    extends Magento_ImportExport_Model_Export_EntityAbstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL           = '_email';
    const COLUMN_WEBSITE         = '_website';
    const COLUMN_FINANCE_WEBSITE = '_finance_website';
    /**#@-*/

    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME =
        'Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection';

    /**
     * XML path to page size parameter
     */
    const XML_PATH_PAGE_SIZE = 'export/customer_page_size/finance';

    /**
     * Website ID-to-code
     *
     * @var array
     */
    protected $_websiteIdToCode = array();

    /**
     * Array of attributes for export
     *
     * @var array
     */
    protected $_entityAttributes;

    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_EMAIL, self::COLUMN_WEBSITE, self::COLUMN_FINANCE_WEBSITE);

    /**
     * Customers whose address are exported
     *
     * @var Magento_ScheduledImportExport_Model_Resource_Customer_Collection
     */
    protected $_customerCollection;

    /**
     * Customers whose financial data is exported
     *
     * @var Magento_ImportExport_Model_Export_Entity_Eav_Customer
     */
    protected $_customerEntity;

    /**
     * Helper to check whether modules are enabled/disabled
     *
     * @var Magento_ScheduledImportExport_Helper_Data
     */
    protected $_importExportData;

    /**
     * @var Magento_ScheduledImportExport_Model_Resource_Customer_CollectionFactory
     */
    protected $_customerCollFactory;

    /**
     * @var Magento_ImportExport_Model_Export_Entity_Eav_CustomerFactory
     */
    protected $_eavCustomerFactory;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_App $app
     * @param Magento_ImportExport_Model_Export_Factory $collectionFactory
     * @param Magento_ImportExport_Model_Resource_CollectionByPagesIteratorFactory $resourceColFactory
     * @param Magento_ScheduledImportExport_Model_Resource_Customer_CollectionFactory $customerCollFactory
     * @param Magento_ImportExport_Model_Export_Entity_Eav_CustomerFactory $eavCustomerFactory
     * @param Magento_ScheduledImportExport_Helper_Data $importExportData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_App $app,
        Magento_ImportExport_Model_Export_Factory $collectionFactory,
        Magento_ImportExport_Model_Resource_CollectionByPagesIteratorFactory $resourceColFactory,
        Magento_ScheduledImportExport_Model_Resource_Customer_CollectionFactory $customerCollFactory,
        Magento_ImportExport_Model_Export_Entity_Eav_CustomerFactory $eavCustomerFactory,
        Magento_ScheduledImportExport_Helper_Data $importExportData,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $app, $collectionFactory, $resourceColFactory, $data);

        $this->_customerCollFactory = $customerCollFactory;
        $this->_eavCustomerFactory = $eavCustomerFactory;
        $this->_importExportData = $importExportData;

        $this->_initFrontendWebsites()
            ->_initWebsites(true);
        $this->setFileName($this->getEntityTypeCode());
    }

    /**
     * Initialize frontend websites
     *
     * @return Magento_ScheduledImportExport_Model_Export_Entity_Customer_Finance
     */
    protected function _initFrontendWebsites()
    {
        /** @var $website Magento_Core_Model_Website */
        foreach ($this->_websiteManager->getWebsites() as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Get customers collection
     *
     * @return Magento_ScheduledImportExport_Model_Resource_Customer_Collection
     */
    protected function _getEntityCollection()
    {
        if (empty($this->_customerCollection)) {
            $this->_customerCollection = $this->_customerCollFactory->create();
        }
        return $this->_customerCollection;
    }

    /**
     * @inheritdoc
     */
    protected function _getHeaderColumns()
    {
        return array_merge($this->getPermanentAttributes(), $this->_getEntityAttributes());
    }

    /**
     * Get list of permanent attributes
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
        $writer = $this->getWriter();

        // create export file
        $writer->setHeaderCols($this->_getHeaderColumns());
        $this->_exportCollectionByPages($this->_getEntityCollection());

        return $writer->getContents();
    }

    /**
     * Export given customer data
     *
     * @param Magento_Customer_Model_Customer $item
     * @return string
     */
    public function exportItem($item)
    {
        $validAttributeCodes = $this->_getEntityAttributes();

        foreach ($this->_websiteIdToCode as $websiteCode) {
            $row = array();
            foreach ($validAttributeCodes as $code) {
                $attributeCode = $websiteCode . '_' . $code;
                $websiteData   = $item->getData($attributeCode);
                if (null !== $websiteData) {
                    $row[$code] = $websiteData;
                }
            }

            if (!empty($row)) {
                $row[self::COLUMN_EMAIL]           = $item->getEmail();
                $row[self::COLUMN_WEBSITE]         = $this->_websiteIdToCode[$item->getWebsiteId()];
                $row[self::COLUMN_FINANCE_WEBSITE] = $websiteCode;
                $this->getWriter()
                    ->writeRow($row);
            }
        }
    }

    /**
     * Set parameters (push filters from post into export customer model)
     *
     * @param array $parameters
     * @return Magento_ImportExport_Model_Export_Entity_Eav_Customer_Address
     */
    public function setParameters(array $parameters)
    {
        if (empty($this->_customerEntity)) {
            $this->_customerEntity = $this->_eavCustomerFactory->create();
        }
        //  push filters from post into export customer model
        $this->_customerEntity->setParameters($parameters);
        $this->_customerEntity->filterEntityCollection($this->_getEntityCollection());

        // join with finance data tables
        if ($this->_importExportData->isRewardPointsEnabled()) {
            $this->_getEntityCollection()->joinWithRewardPoints();
        }

        if ($this->_importExportData->isCustomerBalanceEnabled()) {
            $this->_getEntityCollection()->joinWithCustomerBalance();
        }

        return parent::setParameters($parameters);
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
     * Get attributes for export
     *
     * @return array
     */
    protected function _getEntityAttributes()
    {
        if ($this->_entityAttributes === null) {
            $this->_entityAttributes = array();
            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                /** @var $attribute Magento_Eav_Model_Entity_Attribute */
                $this->_entityAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->_entityAttributes;
    }
}
