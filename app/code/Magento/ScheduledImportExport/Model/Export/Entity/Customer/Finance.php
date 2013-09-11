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
namespace Magento\ScheduledImportExport\Model\Export\Entity\Customer;

class Finance
    extends \Magento\ImportExport\Model\Export\EntityAbstract
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
        '\Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection';

    /**
     * XML path to page size parameter
     */
    const XML_PATH_PAGE_SIZE = 'export/customer_page_size/finance';

    /**
     * Website ID-to-code
     *
     * @var array
     */
    protected $_frontendWebsiteIdToCode = array();

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
     * @var \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
     */
    protected $_customerCollection;

    /**
     * Customers whose financial data is exported
     *
     * @var \Magento\ImportExport\Model\Export\Entity\Eav\Customer
     */
    protected $_customerEntity;

    /**
     * Helper to check whether modules are enabled/disabled
     *
     * @var \Magento\ScheduledImportExport\Helper\Data
     */
    protected $_moduleHelper;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $this->_customerCollection = isset($data['customer_collection']) ? $data['customer_collection']
            : \Mage::getResourceModel('\Magento\ScheduledImportExport\Model\Resource\Customer\Collection');
        $this->_customerEntity = isset($data['customer_entity']) ? $data['customer_entity']
            : \Mage::getModel('\Magento\ImportExport\Model\Export\Entity\Eav\Customer');
        $this->_moduleHelper = isset($data['module_helper']) ? $data['module_helper']
            : \Mage::helper('Magento\ScheduledImportExport\Helper\Data');

        $this->_initFrontendWebsites()
            ->_initWebsites(true);
        $this->setFileName($this->getEntityTypeCode());
    }

    /**
     * Initialize frontend websites
     *
     * @return \Magento\ScheduledImportExport\Model\Export\Entity\Customer\Finance
     */
    protected function _initFrontendWebsites()
    {
        /** @var $website \Magento\Core\Model\Website */
        foreach ($this->_websiteManager->getWebsites() as $website) {
            $this->_frontendWebsiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Get customers collection
     *
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
     */
    protected function _getEntityCollection()
    {
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
     * @param \Magento\Customer\Model\Customer $item
     * @return string
     */
    public function exportItem($item)
    {
        $validAttributeCodes = $this->_getEntityAttributes();

        foreach ($this->_frontendWebsiteIdToCode as $websiteCode) {
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
     * @return \Magento\ImportExport\Model\Export\Entity\Eav\Customer\Address
     */
    public function setParameters(array $parameters)
    {
        //  push filters from post into export customer model
        $this->_customerEntity->setParameters($parameters);
        $this->_customerEntity->filterEntityCollection($this->_getEntityCollection());

        // join with finance data tables
        if ($this->_moduleHelper->isRewardPointsEnabled()) {
            $this->_getEntityCollection()->joinWithRewardPoints();
        }

        if ($this->_moduleHelper->isCustomerBalanceEnabled()) {
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
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $this->_entityAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->_entityAttributes;
    }
}
