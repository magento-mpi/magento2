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
 * Import entity abstract customer model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ImportExport_Model_Import_Entity_Eav_CustomerAbstract
    extends Mage_ImportExport_Model_Import_Entity_EavAbstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_WEBSITE = '_website';
    const COLUMN_EMAIL   = '_email';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_WEBSITE_IS_EMPTY   = 'websiteIsEmpty';
    const ERROR_EMAIL_IS_EMPTY     = 'emailIsEmpty';
    const ERROR_INVALID_WEBSITE    = 'invalidWebsite';
    const ERROR_INVALID_EMAIL      = 'invalidEmail';
    const ERROR_VALUE_IS_REQUIRED  = 'valueIsRequired';
    const ERROR_CUSTOMER_NOT_FOUND = 'customerNotFound';
    /**#@-*/

    /**
     * Array of attribute codes which will be ignored in validation and import procedures.
     * For example, when entity attribute has own validation and import procedures
     * or just to deny this attribute processing.
     *
     * @var array
     */
    protected $_ignoredAttributes = array('website_id', 'store_id', 'default_billing', 'default_shipping');

    /**
     * Customer collection wrapper
     *
     * @var Mage_ImportExport_Model_Resource_Customer_Storage
     */
    protected $_customerStorage;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $this->addMessageTemplate(self::ERROR_WEBSITE_IS_EMPTY,
            __('Website is not specified')
        );
        $this->addMessageTemplate(self::ERROR_EMAIL_IS_EMPTY,
            __('E-mail is not specified')
        );
        $this->addMessageTemplate(self::ERROR_INVALID_WEBSITE,
            __("Invalid value in website column")
        );
        $this->addMessageTemplate(self::ERROR_INVALID_EMAIL,
            __('E-mail is invalid')
        );
        $this->addMessageTemplate(self::ERROR_VALUE_IS_REQUIRED,
            __("Required attribute '%s' has an empty value")
        );
        $this->addMessageTemplate(self::ERROR_CUSTOMER_NOT_FOUND,
            __("Customer with such email and website code doesn't exist")
        );

        $this->_initCustomers($data)
            ->_initWebsites(true);
    }

    /**
     * Initialize existent customers data
     *
     * @param array $data
     * @return Mage_ImportExport_Model_Import_Entity_Eav_CustomerAbstract
     */
    protected function _initCustomers(array $data)
    {
        if (!isset($data['page_size'])) {
            $data['page_size'] = $this->_pageSize;
        }
        $this->_customerStorage = isset($data['customer_storage']) ? $data['customer_storage']
                : Mage::getResourceModel('Mage_ImportExport_Model_Resource_Customer_Storage', array('data' => $data));

        return $this;
    }

    /**
     * Get customer id if customer is present in database
     *
     * @param string $email
     * @param string $websiteCode
     * @return bool|int
     */
    protected function _getCustomerId($email, $websiteCode)
    {
        $email = strtolower(trim($email));
        if (isset($this->_websiteCodeToId[$websiteCode])) {
            $websiteId = $this->_websiteCodeToId[$websiteCode];
            return $this->_customerStorage->getCustomerId($email, $websiteId);
        }

        return false;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) { // check that row is already validated
            return !isset($this->_invalidRows[$rowNumber]);
        }
        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;

        if ($this->getBehavior($rowData) == Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE) {
            $this->_validateRowForUpdate($rowData, $rowNumber);
        } elseif ($this->getBehavior($rowData) == Mage_ImportExport_Model_Import::BEHAVIOR_DELETE) {
            $this->_validateRowForDelete($rowData, $rowNumber);
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Validate data row for add/update behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return null
     */
    abstract protected function _validateRowForUpdate(array $rowData, $rowNumber);

    /**
     * Validate data row for delete behaviour
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return null
     */
    abstract protected function _validateRowForDelete(array $rowData, $rowNumber);

    /**
     * General check of unique key
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    protected function _checkUniqueKey(array $rowData, $rowNumber)
    {
        if (empty($rowData[static::COLUMN_WEBSITE])) {
            $this->addRowError(static::ERROR_WEBSITE_IS_EMPTY, $rowNumber, static::COLUMN_WEBSITE);
        } elseif (empty($rowData[static::COLUMN_EMAIL])) {
            $this->addRowError(static::ERROR_EMAIL_IS_EMPTY, $rowNumber, static::COLUMN_EMAIL);
        } else {
            $email   = strtolower($rowData[static::COLUMN_EMAIL]);
            $website = $rowData[static::COLUMN_WEBSITE];

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(static::ERROR_INVALID_EMAIL, $rowNumber, static::COLUMN_EMAIL);
            } elseif (!isset($this->_websiteCodeToId[$website])) {
                $this->addRowError(static::ERROR_INVALID_WEBSITE, $rowNumber, static::COLUMN_WEBSITE);
            }
        }
        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Get customer storage
     *
     * @return Mage_ImportExport_Model_Resource_Customer_Storage
     */
    public function getCustomerStorage()
    {
        return $this->_customerStorage;
    }
}
