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
 * Import entity customer address model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
{
    /**#@+
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute.
     * This name convention is for to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL      = '_email';
    const COLUMN_WEBSITE    = '_website';
    const COLUMN_ADDRESS_ID = '_entity_id';
    /**#@-*/

    /**#@+
     * Particular columns that contains of customer default addresses
     */
    const COLUMN_NAME_DEFAULT_BILLING  = '_address_default_billing_';
    const COLUMN_NAME_DEFAULT_SHIPPING = '_address_default_shipping_';
    /**#@-*/

    /**
     * Default addresses column names to appropriate customer attribute code.
     *
     * @var array
     */
    protected static $_defaultAddressAttributeMapping = array(
        self::COLUMN_NAME_DEFAULT_BILLING  => 'default_billing',
        self::COLUMN_NAME_DEFAULT_SHIPPING => 'default_shipping'
    );


    /**
     * Permanent entity columns
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COLUMN_WEBSITE, self::COLUMN_EMAIL, self::COLUMN_ADDRESS_ID);

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_particularAttributes = array(
        self::COLUMN_WEBSITE,
        self::COLUMN_EMAIL,
        self::COLUMN_ADDRESS_ID,
        self::COLUMN_NAME_DEFAULT_BILLING,
        self::COLUMN_NAME_DEFAULT_SHIPPING
    );

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initWebsites()
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
        return 'customer_address';
    }

    /**
     * Customer default addresses column name to customer attribute mapping array.
     *
     * @static
     * @return array
     */
    public static function getDefaultAddressAttributeMapping()
    {
        return self::$_defaultAddressAttributeMapping;
    }

    // @codingStandardsIgnoreStart

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        // TODO: need to implement
        return true;
    }

    // @codingStandardsIgnoreEnd

    /**
     * Retrieve entity attribute EAV collection
     *
     * @return Mage_Eav_Model_Resource_Attribute_Collection
     */
    protected function _getAttributeCollection()
    {
        /** @var $addressCollection Mage_Customer_Model_Resource_Address_Attribute_Collection */
        $addressCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Address_Attribute_Collection');
        $addressCollection->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        return $addressCollection;
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
