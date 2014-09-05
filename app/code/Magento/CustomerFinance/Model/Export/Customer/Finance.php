<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerFinance\Model\Export\Customer;

/**
 * Export customer finance entity model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method      array getData()
 */
class Finance extends \Magento\ImportExport\Model\Export\AbstractEntity
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL = '_email';

    const COLUMN_WEBSITE = '_website';

    const COLUMN_FINANCE_WEBSITE = '_finance_website';

    /**#@-*/

    /**
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = 'Magento\CustomerFinance\Model\Resource\Customer\Attribute\Finance\Collection';

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
     * @var string[]
     */
    protected $_entityAttributes;

    /**
     * Permanent entity columns
     *
     * @var string[]
     */
    protected $_permanentAttributes = array(self::COLUMN_EMAIL, self::COLUMN_WEBSITE, self::COLUMN_FINANCE_WEBSITE);

    /**
     * Customers whose address are exported
     *
     * @var \Magento\CustomerFinance\Model\Resource\Customer\Collection
     */
    protected $_customerCollection;

    /**
     * Customers whose financial data is exported
     *
     * @var \Magento\CustomerImportExport\Model\Export\Customer
     */
    protected $_customerEntity;

    /**
     * Helper to check whether modules are enabled/disabled
     *
     * @var \Magento\CustomerFinance\Helper\Data
     */
    protected $_customerFinanceData;

    /**
     * @var \Magento\CustomerFinance\Model\Resource\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var \Magento\CustomerImportExport\Model\Export\CustomerFactory
     */
    protected $_eavCustomerFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\ImportExport\Model\Export\Factory $collectionFactory
     * @param \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory
     * @param \Magento\CustomerFinance\Model\Resource\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\CustomerImportExport\Model\Export\CustomerFactory $eavCustomerFactory
     * @param \Magento\CustomerFinance\Helper\Data $customerFinanceData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\ImportExport\Model\Export\Factory $collectionFactory,
        \Magento\ImportExport\Model\Resource\CollectionByPagesIteratorFactory $resourceColFactory,
        \Magento\CustomerFinance\Model\Resource\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\CustomerImportExport\Model\Export\CustomerFactory $eavCustomerFactory,
        \Magento\CustomerFinance\Helper\Data $customerFinanceData,
        array $data = array()
    ) {
        parent::__construct($scopeConfig, $storeManager, $collectionFactory, $resourceColFactory, $data);

        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_eavCustomerFactory = $eavCustomerFactory;
        $this->_customerFinanceData = $customerFinanceData;

        $this->_initFrontendWebsites()->_initWebsites(true);
        $this->setFileName($this->getEntityTypeCode());
    }

    /**
     * Initialize frontend websites
     *
     * @return $this
     */
    protected function _initFrontendWebsites()
    {
        /** @var $website \Magento\Store\Model\Website */
        foreach ($this->_storeManager->getWebsites() as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Get customers collection
     *
     * @return \Magento\CustomerFinance\Model\Resource\Customer\Collection
     */
    protected function _getEntityCollection()
    {
        if (empty($this->_customerCollection)) {
            $this->_customerCollection = $this->_customerCollectionFactory->create();
        }
        return $this->_customerCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHeaderColumns()
    {
        return array_merge($this->getPermanentAttributes(), $this->_getEntityAttributes());
    }

    /**
     * Get list of permanent attributes
     *
     * @return string[]
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
     * @param \Magento\Customer\Model\Customer $item
     * @return void
     */
    public function exportItem($item)
    {
        $validAttributeCodes = $this->_getEntityAttributes();

        foreach ($this->_websiteIdToCode as $websiteCode) {
            $row = array();
            foreach ($validAttributeCodes as $code) {
                $attributeCode = $websiteCode . '_' . $code;
                $websiteData = $item->getData($attributeCode);
                if (null !== $websiteData) {
                    $row[$code] = $websiteData;
                }
            }

            if (!empty($row)) {
                $row[self::COLUMN_EMAIL] = $item->getEmail();
                $row[self::COLUMN_WEBSITE] = $this->_websiteIdToCode[$item->getWebsiteId()];
                $row[self::COLUMN_FINANCE_WEBSITE] = $websiteCode;
                $this->getWriter()->writeRow($row);
            }
        }
    }

    /**
     * Set parameters (push filters from post into export customer model)
     *
     * @param string[] $parameters
     * @return \Magento\CustomerImportExport\Model\Export\Address
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
        if ($this->_customerFinanceData->isRewardPointsEnabled()) {
            $this->_getEntityCollection()->joinWithRewardPoints();
        }

        if ($this->_customerFinanceData->isCustomerBalanceEnabled()) {
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
     * @return string[]
     */
    protected function _getEntityAttributes()
    {
        if ($this->_entityAttributes === null) {
            $this->_entityAttributes = array();
            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $this->_entityAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->_entityAttributes;
    }
}
