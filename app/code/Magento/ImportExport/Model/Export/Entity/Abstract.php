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
abstract class Magento_ImportExport_Model_Export_Entity_Abstract
{
    /**
     * Attribute code to its values. Only attributes with options and only default store values used.
     *
     * @var array
     */
    protected $_attributeValues = array();


    /**
     * Attribute code to its values. Only attributes with options and only default store values used.
     *
     * @var array
     */
    protected static $attrCodes = null;

    /**
     * DB connection.
     *
     * @var Magento_DB_Adapter_Pdo_Mysql
     */
    protected $_connection;

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array();

    /**
     * Entity type id.
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Error codes with arrays of corresponding row numbers.
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Error counter.
     *
     * @var int
     */
    protected $_errorsCount = 0;

    /**
     * Limit of errors after which pre-processing will exit.
     *
     * @var int
     */
    protected $_errorsLimit = 100;

    /**
     * Export filter data.
     *
     * @var array
     */
    protected $_filter = array();

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array();

    /**
     * Validation failure message template definitions.
     *
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * Parameters.
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_specialAttributes = array();

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = array();

    /**
     * Number of entities processed by validation.
     *
     * @var int
     */
    protected $_processedEntitiesCount = 0;

    /**
     * Number of rows processed by validation.
     *
     * @var int
     */
    protected $_processedRowsCount = 0;

    /**
     * Source model.
     *
     * @var Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $entityCode = $this->getEntityTypeCode();
        $this->_entityTypeId = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType($entityCode)->getEntityTypeId();
        $this->_connection   = Mage::getSingleton('Magento_Core_Model_Resource')->getConnection('write');
    }

    /**
     * Initialize stores hash.
     *
     * @return Magento_ImportExport_Model_Export_Entity_Abstract
     */
    protected function _initStores()
    {
        foreach (Mage::app()->getStores(true) as $store) {
            $this->_storeIdToCode[$store->getId()] = $store->getCode();
        }
        ksort($this->_storeIdToCode); // to ensure that 'admin' store (ID is zero) goes first

        return $this;
    }

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
     * Get attributes codes which are appropriate for export.
     *
     * @return array
     */
    protected function _getExportAttrCodes()
    {
        if (null === self::$attrCodes) {
            if (!empty($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])
                    && is_array($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP])) {
                $skipAttr = array_flip($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP]);
            } else {
                $skipAttr = array();
            }
            $attrCodes = array();

            foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
                if (!isset($skipAttr[$attribute->getAttributeId()])
                        || in_array($attribute->getAttributeCode(), $this->_permanentAttributes)) {
                    $attrCodes[] = $attribute->getAttributeCode();
                }
            }
            self::$attrCodes = $attrCodes;
        }
        return self::$attrCodes;
    }

    /**
     * Initialize attribute option values.
     *
     * @return Magento_ImportExport_Model_Export_Entity_Abstract
     */
    protected function _initAttrValues()
    {
        foreach ($this->getAttributeCollection() as $attribute) {
            $this->_attributeValues[$attribute->getAttributeCode()] = $this->getAttributeOptions($attribute);
        }
        return $this;
    }

    /**
     * Apply filter to collection and add not skipped attributes to select.
     *
     * @param Magento_Eav_Model_Entity_Collection_Abstract $collection
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _prepareEntityCollection(Magento_Eav_Model_Entity_Collection_Abstract $collection)
    {
        if (!isset($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])
            || !is_array($this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])) {
            $exportFilter = array();
        } else {
            $exportFilter = $this->_parameters[Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP];
        }
        $exportAttrCodes = $this->_getExportAttrCodes();

        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attrCode = $attribute->getAttributeCode();

            // filter applying
            if (isset($exportFilter[$attrCode])) {
                $attrFilterType = Magento_ImportExport_Model_Export::getAttributeFilterType($attribute);

                if (Magento_ImportExport_Model_Export::FILTER_TYPE_SELECT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('eq' => $exportFilter[$attrCode]));
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_INPUT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('like' => "%{$exportFilter[$attrCode]}%"));
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_DATE == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to   = array_shift($exportFilter[$attrCode]);

                        if (is_scalar($from) && !empty($from)) {
                            $date = Mage::app()->getLocale()->date($from,null,null,false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('from' => $date, 'date' => true));
                        }
                        if (is_scalar($to) && !empty($to)) {
                            $date = Mage::app()->getLocale()->date($to,null,null,false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('to' => $date, 'date' => true));
                        }
                    }
                } elseif (Magento_ImportExport_Model_Export::FILTER_TYPE_NUMBER == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to   = array_shift($exportFilter[$attrCode]);

                        if (is_numeric($from)) {
                            $collection->addAttributeToFilter($attrCode, array('from' => $from));
                        }
                        if (is_numeric($to)) {
                            $collection->addAttributeToFilter($attrCode, array('to' => $to));
                        }
                    }
                }
            }
            if (in_array($attrCode, $exportAttrCodes)) {
                $collection->addAttributeToSelect($attrCode);
            }
        }
        return $collection;
    }

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     * @return Magento_ImportExport_Model_Import_SourceAbstract
     */
    public function addRowError($errorCode, $errorRowNum)
    {
        $this->_errors[$errorCode][] = $errorRowNum + 1; // one added for human readability
        $this->_invalidRows[$errorRowNum] = true;
        $this->_errorsCount ++;

        return $this;
    }

    /**
     * Add message template for specific error code from outside.
     *
     * @param string $errorCode Error code
     * @param string $message Message template
     * @return Magento_ImportExport_Model_Import_Entity_Abstract
     */
    public function addMessageTemplate($errorCode, $message)
    {
        $this->_messageTemplates[$errorCode] = $message;

        return $this;
    }

    /**
     * Export process.
     *
     * @return string
     */
    abstract public function export();

    /**
     * Clean up attribute collection.
     *
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function filterAttributeCollection(Magento_Eav_Model_Resource_Entity_Attribute_Collection $collection)
    {
        $collection->load();

        foreach ($collection as $attribute) {
            if (in_array($attribute->getAttributeCode(), $this->_disabledAttrs)) {
                $collection->removeItemByKey($attribute->getId());
            }
        }
        return $collection;
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Collection
     */
    abstract public function getAttributeCollection();

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return array
     */
    public function getAttributeOptions(Magento_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        $options = array();

        if ($attribute->usesSource()) {
            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $this->_indexValueAttributes) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    foreach (is_array($option['value']) ? $option['value'] : array($option) as $innerOption) {
                        if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                            $options[$innerOption['value']] = $innerOption[$index];
                        }
                    }
                }
            } catch (Exception $e) {
                // ignore exceptions connected with source models
            }
        }
        return $options;
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    abstract public function getEntityTypeCode();

    /**
     * Entity type ID getter.
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->_entityTypeId;
    }

    /**
     * Returns error information.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $translator = Mage::helper('Magento_ImportExport_Helper_Data');
        $messages = array();
        foreach ($this->_errors as $errorCode => $errorRows) {
            $message = isset($this->_messageTemplates[$errorCode])
                ? $translator->__($this->_messageTemplates[$errorCode])
                : $translator->__("Please correct the value for '%s' column", $errorCode);
            $messages[$message] = $errorRows;
        }
        return $messages;
    }

    /**
     * Returns error counter value.
     *
     * @return int
     */
    public function getErrorsCount()
    {
        return $this->_errorsCount;
    }

    /**
     * Returns invalid rows count.
     *
     * @return int
     */
    public function getInvalidRowsCount()
    {
        return count($this->_invalidRows);
    }

    /**
     * Returns number of checked entities.
     *
     * @return int
     */
    public function getProcessedEntitiesCount()
    {
        return $this->_processedEntitiesCount;
    }

    /**
     * Returns number of checked rows.
     *
     * @return int
     */
    public function getProcessedRowsCount()
    {
        return $this->_processedRowsCount;
    }

    /**
     * Inner writer object getter.
     *
     * @throws Exception
     * @return Magento_ImportExport_Model_Export_Adapter_Abstract
     */
    public function getWriter()
    {
        if (!$this->_writer) {
            Mage::throwException(Mage::helper('Magento_ImportExport_Helper_Data')->__('Please specify writer.'));
        }
        return $this->_writer;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     * @return Magento_ImportExport_Model_Export_Entity_Abstract
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Writer model setter.
     *
     * @param Magento_ImportExport_Model_Export_Adapter_Abstract $writer
     * @return Magento_ImportExport_Model_Export_Entity_Abstract
     */
    public function setWriter(Magento_ImportExport_Model_Export_Adapter_Abstract $writer)
    {
        $this->_writer = $writer;

        return $this;
    }
}
