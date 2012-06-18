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
 * Import entity customer model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL   = 'email';
    const COLUMN_WEBSITE = '_website';
    const COLUMN_STORE   = '_store';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_INVALID_WEBSITE      = 'invalidWebsite';
    const ERROR_INVALID_EMAIL        = 'invalidEmail';
    const ERROR_DUPLICATE_EMAIL_SITE = 'duplicateEmailSite';
    const ERROR_EMAIL_IS_EMPTY       = 'emailIsEmpty';
    const ERROR_ROW_IS_ORPHAN        = 'rowIsOrphan';
    const ERROR_VALUE_IS_REQUIRED    = 'valueIsRequired';
    const ERROR_INVALID_STORE        = 'invalidStore';
    const ERROR_EMAIL_SITE_NOT_FOUND = 'emailSiteNotFound';
    const ERROR_PASSWORD_LENGTH      = 'passwordLength';
    /**#@-*/

    /**
     * Minimum password length
     */
    const MIN_PASSWORD_LENGTH = 6;

    /**
     * Customers information from import file
     *
     * @var array
     */
    protected $_newCustomers = array();

    /**
     * Array of attribute codes which will be ignored in validation and import procedures.
     * For example, when entity attribute has own validation and import procedures
     * or just to deny this attribute processing.
     *
     * @var array
     */
    protected $_ignoredAttributes = array('website_id', 'store_id', 'default_billing', 'default_shipping');

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_particularAttributes[] = self::COLUMN_WEBSITE;
        $this->_particularAttributes[] = self::COLUMN_STORE;
        $this->_permanentAttributes[]  = self::COLUMN_EMAIL;
        $this->_permanentAttributes[]  = self::COLUMN_WEBSITE;

        $this->_initWebsites()
            ->_initStores()
            ->_initAttributes();
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     */
    protected function _importData()
    {
        // TODO: need to implement
        return false;
    }

    /**
     * EAV entity type code getter
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Retrieve customer attribute EAV collection
     *
     * @return Mage_Customer_Model_Resource_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        /** @var $collection Mage_Customer_Model_Resource_Attribute_Collection */
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $collection->addSystemHiddenFilterWithPasswordHash();
        return $collection;
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
        $email   = strtolower($rowData[self::COLUMN_EMAIL]);
        $website = $rowData[self::COLUMN_WEBSITE];

        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $this->addRowError(self::ERROR_INVALID_EMAIL, $rowNumber);
        } elseif (!isset($this->_websiteCodeToId[$website])) {
            $this->addRowError(self::ERROR_INVALID_WEBSITE, $rowNumber);
        } else {
            if (isset($this->_newCustomers[strtolower($rowData[self::COLUMN_EMAIL])][$website])) {
                $this->addRowError(self::ERROR_DUPLICATE_EMAIL_SITE, $rowNumber);
            }
            $this->_newCustomers[$email][$website] = false;

            if (!empty($rowData[self::COLUMN_STORE]) && !isset($this->_storeCodeToId[$rowData[self::COLUMN_STORE]])) {
                $this->addRowError(self::ERROR_INVALID_STORE, $rowNumber);
            }
            // check password
            /** @var $stringHelper Mage_Core_Helper_String */
            $stringHelper = Mage::helper('Mage_Core_Helper_String');
            if (isset($rowData['password']) && strlen($rowData['password'])
                && $stringHelper->strlen($rowData['password']) < self::MIN_PASSWORD_LENGTH
            ) {
                $this->addRowError(self::ERROR_PASSWORD_LENGTH, $rowNumber);
            }
            // check simple attributes
            foreach ($this->_attributes as $attributeCode => $attributeParams) {
                if (in_array($attributeCode, $this->_ignoredAttributes)) {
                    continue;
                }
                if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                    $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                } elseif ($attributeParams['is_required'] && $this->_loadCustomerData($email, $website)) {
                    $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                }
            }
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Load data of existed customer by email and website code
     *
     * @param $email
     * @param $websiteCode
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerData($email, $websiteCode)
    {
        if (isset($this->_websiteCodeToId[$websiteCode])) {
            /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection');
            $collection->addAttributeToFilter('email', $email)
                ->addAttributeToFilter('website_id', $this->_websiteCodeToId[$websiteCode]);
            $customer = $collection->getFirstItem();
            if ($customer->getId()) {
                return $customer;
            } else {
                return false;
            }
        } else {
            Mage::throwException(
                Mage::helper('Mage_ImportExport_Helper_Data')->__('Unknown website code')
            );
        }
    }
}
