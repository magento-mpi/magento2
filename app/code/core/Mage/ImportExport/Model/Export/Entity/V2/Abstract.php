<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export entity abstract model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ImportExport_Model_Export_Entity_V2_Abstract
{
    /**
     * DB connection.
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_connection;

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
     * Validation information about processed rows.
     *
     * @var array
     */
    protected $_invalidRows = array();

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
     * @var Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected $_writer;

    /**
     * Array of pairs store ID to its code.
     *
     * @var array
     */
    protected $_storeIdToCode = array();

    /**
     * Website ID-to-code.
     *
     * @var array
     */
    protected $_websiteIdToCode = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_connection   = Mage::getSingleton('Mage_Core_Model_Resource')->getConnection('write');
    }

    /**
     * Initialize stores hash.
     *
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
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
     * Initialize website values.
     *
     * @param bool $withDefault
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
     */
    protected function _initWebsites($withDefault = false)
    {
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites($withDefault) as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
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
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
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
     * Returns error information.
     *
     * @return array
     */
    public function getErrorMessages()
    {
        $translator = Mage::helper('Mage_ImportExport_Helper_Data');
        $messages = array();
        foreach ($this->_errors as $errorCode => $errorRows) {
            $message = isset($this->_messageTemplates[$errorCode])
                ? $translator->__($this->_messageTemplates[$errorCode])
                : $translator->__("Invalid value for '%s' column", $errorCode);
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
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
     */
    public function getWriter()
    {
        if (!$this->_writer) {
            Mage::throwException(Mage::helper('Mage_ImportExport_Helper_Data')->__('No writer specified'));
        }
        return $this->_writer;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Writer model setter.
     *
     * @param Mage_ImportExport_Model_Export_Adapter_Abstract $writer
     * @return Mage_ImportExport_Model_Export_Entity_V2_Abstract
     */
    public function setWriter(Mage_ImportExport_Model_Export_Adapter_Abstract $writer)
    {
        $this->_writer = $writer;

        return $this;
    }
}
