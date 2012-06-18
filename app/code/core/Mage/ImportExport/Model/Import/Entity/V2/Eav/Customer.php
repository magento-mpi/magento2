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
     * Existing customers information. In form of:
     *
     * [customer e-mail] => array(
     *    [website code 1] => customer_id 1,
     *    [website code 2] => customer_id 2,
     *           ...       =>     ...      ,
     *    [website code n] => customer_id n,
     * )
     *
     * @var array
     */
    protected $_oldCustomers = array();

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
        $this->_indexValueAttributes[] ='group_id';

        $this->_initWebsites(true)
            ->_initStores(true)
            ->_initAttributes()
            ->_initCustomers();
    }

    /**
     * Initialize existent customers data
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer
     */
    protected function _initCustomers()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        foreach (Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection') as $customer) {
            $email = strtolower($customer->getEmail());
            if (!isset($this->_oldCustomers[$email])) {
                $this->_oldCustomers[$email] = array();
            }
            $this->_oldCustomers[$email][$customer->getWebsiteId()] = $customer->getId();
        }

        return $this;
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
                } elseif ($attributeParams['is_required'] && !$this->_isCustomerExists($email, $website)) {
                    $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                }
            }
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Check is customer existed in database
     *
     * @param string $email
     * @param string $websiteCode
     * @return bool
     */
    protected function _isCustomerExists($email, $websiteCode)
    {
        if (isset($this->_websiteCodeToId[$websiteCode])) {
            $websiteId = $this->_websiteCodeToId[$websiteCode];
            return isset($this->_oldCustomers[$email][$websiteId]);
        }

        return false;
    }
}
