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
namespace Magento\ImportExport\Model\Export\Entity;

abstract class AbstractEntity
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
     * @var \Magento\DB\Adapter\Pdo\Mysql
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
     * @var \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
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
        $this->_entityTypeId = \Mage::getSingleton('Magento\Eav\Model\Config')->getEntityType($entityCode)->getEntityTypeId();
        $this->_connection   = \Mage::getSingleton('Magento\Core\Model\Resource')->getConnection('write');
    }

    /**
     * Initialize stores hash.
     *
     * @return \Magento\ImportExport\Model\Export\Entity\AbstractEntity
     */
    protected function _initStores()
    {
        foreach (\Mage::app()->getStores(true) as $store) {
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
     * @return \Magento\Data\Collection\Db
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
            if (!empty($this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_SKIP])
                    && is_array($this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_SKIP])) {
                $skipAttr = array_flip($this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_SKIP]);
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
     * @return \Magento\ImportExport\Model\Export\Entity\AbstractEntity
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
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _prepareEntityCollection(\Magento\Eav\Model\Entity\Collection\AbstractCollection $collection)
    {
        if (!isset($this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_GROUP])
            || !is_array($this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_GROUP])) {
            $exportFilter = array();
        } else {
            $exportFilter = $this->_parameters[\Magento\ImportExport\Model\Export::FILTER_ELEMENT_GROUP];
        }
        $exportAttrCodes = $this->_getExportAttrCodes();

        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attrCode = $attribute->getAttributeCode();

            // filter applying
            if (isset($exportFilter[$attrCode])) {
                $attrFilterType = \Magento\ImportExport\Model\Export::getAttributeFilterType($attribute);

                if (\Magento\ImportExport\Model\Export::FILTER_TYPE_SELECT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('eq' => $exportFilter[$attrCode]));
                    }
                } elseif (\Magento\ImportExport\Model\Export::FILTER_TYPE_INPUT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('like' => "%{$exportFilter[$attrCode]}%"));
                    }
                } elseif (\Magento\ImportExport\Model\Export::FILTER_TYPE_DATE == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to   = array_shift($exportFilter[$attrCode]);

                        if (is_scalar($from) && !empty($from)) {
                            $date = \Mage::app()->getLocale()->date($from,null,null,false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('from' => $date, 'date' => true));
                        }
                        if (is_scalar($to) && !empty($to)) {
                            $date = \Mage::app()->getLocale()->date($to,null,null,false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('to' => $date, 'date' => true));
                        }
                    }
                } elseif (\Magento\ImportExport\Model\Export::FILTER_TYPE_NUMBER == $attrFilterType) {
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
     * @return \Magento\ImportExport\Model\Import\SourceAbstract
     */
    public function addRowError($errorCode, $errorRowNum)
    {
        $errorCode = (string)$errorCode;
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
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEntity
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
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    public function filterAttributeCollection(\Magento\Eav\Model\Resource\Entity\Attribute\Collection $collection)
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
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Collection
     */
    abstract public function getAttributeCollection();

    /**
     * Returns attributes all values in label-value or value-value pairs form. Labels are lower-cased.
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return array
     */
    public function getAttributeOptions(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute)
    {
        $options = array();

        if ($attribute->usesSource()) {
            // should attribute has index (option value) instead of a label?
            $index = in_array($attribute->getAttributeCode(), $this->_indexValueAttributes) ? 'value' : 'label';

            // only default (admin) store values used
            $attribute->setStoreId(\Magento\Catalog\Model\AbstractModel::DEFAULT_STORE_ID);

            try {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    foreach (is_array($option['value']) ? $option['value'] : array($option) as $innerOption) {
                        if (strlen($innerOption['value'])) { // skip ' -- Please Select -- ' option
                            $options[$innerOption['value']] = $innerOption[$index];
                        }
                    }
                }
            } catch (\Exception $e) {
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
        $messages = array();
        foreach ($this->_errors as $errorCode => $errorRows) {
            $message = isset($this->_messageTemplates[$errorCode])
                ? __($this->_messageTemplates[$errorCode])
                : __("Please correct the value for '%1' column", $errorCode);
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
     * @throws \Exception
     * @return \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter
     */
    public function getWriter()
    {
        if (!$this->_writer) {
            \Mage::throwException(__('Please specify writer.'));
        }
        return $this->_writer;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     * @return \Magento\ImportExport\Model\Export\Entity\AbstractEntity
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Writer model setter.
     *
     * @param \Magento\ImportExport\Model\Export\Adapter\AbstractAdapter $writer
     * @return \Magento\ImportExport\Model\Export\Entity\AbstractEntity
     */
    public function setWriter(\Magento\ImportExport\Model\Export\Adapter\AbstractAdapter $writer)
    {
        $this->_writer = $writer;

        return $this;
    }
}
