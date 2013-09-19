<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export entity abstract model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_ImportExport_Model_Export_EntityAbstract
{
    /**#@+
     * Attribute collection name
     */
    const ATTRIBUTE_COLLECTION_NAME = 'Magento_Data_Collection';
    /**#@-*/

    /**#@+
     * XML path to page size parameter
     */
    const XML_PATH_PAGE_SIZE = '';
    /**#@-*/

    /**
     * Website manager (currently Magento_Core_Model_App works as website manager)
     *
     * @var Magento_Core_Model_App
     */
    protected $_websiteManager;

    /**
     * Store manager (currently Magento_Core_Model_App works as store manager)
     *
     * @var Magento_Core_Model_App
     */
    protected $_storeManager;

    /**
     * Error codes with arrays of corresponding row numbers
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Error counter
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Limit of errors after which pre-processing will exit
     *
     * @var int
     */
    protected $_errorsLimit = 100;

    /**
     * Validation information about processed rows
     *
     * @var array
     */
    protected $_invalidRows = array();

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * Parameters
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Number of entities processed by validation
     *
     * @var int
     */
    protected $_processedEntitiesCount = 0;

    /**
     * Number of rows processed by validation
     *
     * @var int
     */
    protected $_processedRowsCount = 0;

    /**
     * Source model
     *
     * @var Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * Array of pairs store ID to its code
     *
     * @var array
     */
    protected $_storeIdToCode = array();

    /**
     * Website ID-to-code
     *
     * @var array
     */
    protected $_websiteIdToCode = array();

    /**
     * Disabled attributes
     *
     * @var array
     */
    protected $_disabledAttributes = array();

    /**
     * Export file name
     *
     * @var string|null
     */
    protected $_fileName = null;

    /**
     * Address attributes collection
     *
     * @var Magento_Data_Collection
     */
    protected $_attributeCollection;

    /**
     * Number of items to fetch from db in one query
     *
     * @var int
     */
    protected $_pageSize;

    /**
     * Collection by pages iterator
     *
     * @var Magento_ImportExport_Model_Resource_CollectionByPagesIterator
     */
    protected $_byPagesIterator;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_websiteManager = isset($data['website_manager']) ? $data['website_manager'] : Mage::app();
        $this->_storeManager   = isset($data['store_manager']) ? $data['store_manager'] : Mage::app();
        $this->_attributeCollection = isset($data['attribute_collection']) ? $data['attribute_collection']
            : Mage::getResourceModel(static::ATTRIBUTE_COLLECTION_NAME);
        $this->_pageSize = isset($data['page_size']) ? $data['page_size']
            : (static::XML_PATH_PAGE_SIZE ? (int) $this->_coreStoreConfig->getConfig(static::XML_PATH_PAGE_SIZE) : 0);
        $this->_byPagesIterator = isset($data['collection_by_pages_iterator']) ? $data['collection_by_pages_iterator']
            : Mage::getResourceModel('Magento_ImportExport_Model_Resource_CollectionByPagesIterator');
    }

    /**
     * Initialize stores hash
     *
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    protected function _initStores()
    {
        /** @var $store Magento_Core_Model_Store */
        foreach ($this->_storeManager->getStores(true) as $store) {
            $this->_storeIdToCode[$store->getId()] = $store->getCode();
        }
        ksort($this->_storeIdToCode); // to ensure that 'admin' store (ID is zero) goes first

        return $this;
    }

    /**
     * Initialize website values
     *
     * @param bool $withDefault
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    protected function _initWebsites($withDefault = false)
    {
        /** @var $website Magento_Core_Model_Website */
        foreach ($this->_websiteManager->getWebsites($withDefault) as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Add error with corresponding current data source row number
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    public function addRowError($errorCode, $errorRowNum)
    {
        $errorCode = (string)$errorCode;
        $this->_errors[$errorCode][] = $errorRowNum + 1; // one added for human readability
        $this->_invalidRows[$errorRowNum] = true;
        $this->_errorsCount++;

        return $this;
    }

    /**
     * Add message template for specific error code from outside
     *
     * @param string $errorCode Error code
     * @param string $message Message template
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    public function addMessageTemplate($errorCode, $message)
    {
        $this->_messageTemplates[$errorCode] = $message;

        return $this;
    }

    /**
     * Export process
     *
     * @return string
     */
    abstract public function export();

    /**
     * Export one item
     *
     * @param Magento_Core_Model_Abstract $item
     */
    abstract public function exportItem($item);

    /**
     * Iterate through given collection page by page and export items
     *
     * @param Magento_Data_Collection_Db $collection
     */
    protected function _exportCollectionByPages(Magento_Data_Collection_Db $collection)
    {
        $this->_byPagesIterator->iterate($collection, $this->_pageSize, array(array($this, 'exportItem')));
    }

    /**
     * Entity type code getter
     *
     * @abstract
     * @return string
     */
    abstract public function getEntityTypeCode();

    /**
     * Get header columns
     *
     * @return array
     */
    abstract protected function _getHeaderColumns();

    /**
     * Get entity collection
     *
     * @return Magento_Data_Collection_Db
     */
    abstract protected function _getEntityCollection();

    /**
     * Entity attributes collection getter
     *
     * @return Magento_Data_Collection
     */
    public function getAttributeCollection()
    {
        return $this->_attributeCollection;
    }

    /**
     * Clean up attribute collection
     *
     * @param Magento_Data_Collection $collection
     * @return Magento_Data_Collection
     */
    public function filterAttributeCollection(Magento_Data_Collection $collection)
    {
        /** @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
        foreach ($collection as $attribute) {
            if (in_array($attribute->getAttributeCode(), $this->_disabledAttributes)) {
                $collection->removeItemByKey($attribute->getId());
            }
        }

        return $collection;
    }

    /**
     * Returns error information
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $messages = array();
        foreach ($this->_errors as $errorCode => $errorRows) {
            $message = isset($this->_messageTemplates[$errorCode])
                ? __($this->_messageTemplates[$errorCode])
                : __("Please correct the value for '%1' column", $errorCode);
            $message = (string)$message;
            $messages[$message] = $errorRows;
        }

        return $messages;
    }

    /**
     * Returns error counter value
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_errorsCount;
    }

    /**
     * Returns invalid rows count
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return count($this->_invalidRows);
    }

    /**
     * Returns number of checked entities
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_processedEntitiesCount;
    }

    /**
     * Returns number of checked rows
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_processedRowsCount;
    }

    /**
     * Inner writer object getter
     *
     * @throws Exception
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    public function getWriter()
    {
        if (!$this->_writer) {
            Mage::throwException(__('Please specify writer.'));
        }

        return $this->_writer;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Writer model setter
     *
     * @param Magento_ImportExport_Model_Export_Adapter_Abstract $writer
     * @return Magento_ImportExport_Model_Export_EntityAbstract
     */
    public function setWriter(Magento_ImportExport_Model_Export_Adapter_Abstract $writer)
    {
        $this->_writer = $writer;

        return $this;
    }

    /**
     * Set export file name
     *
     * @param null|string $fileName
     */
    public function setFileName($fileName)
    {
        $this->_fileName = $fileName;
    }

    /**
     * Get export file name
     *
     * @return null|string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * Retrieve list of disabled attributes codes
     *
     * @return array
     */
    public function getDisabledAttributes()
    {
        return $this->_disabledAttributes;
    }
}
