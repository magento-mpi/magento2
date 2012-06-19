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
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute.
     * This name convention is for to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL      = '_email';
    const COLUMN_ADDRESS_ID = '_entity_id';
    /**#@-*/

    /**#@+
     * Particular columns that contains of customer default addresses
     */
    const COLUMN_NAME_DEFAULT_BILLING  = '_address_default_billing_';
    const COLUMN_NAME_DEFAULT_SHIPPING = '_address_default_shipping_';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_ADDRESS_ID_IS_EMPTY = 'addressIdIsEmpty';
    const ERROR_CUSTOMER_NOT_FOUND  = 'customerNotFound';
    const ERROR_INVALID_REGION      = 'invalidRegion';
    /**#@-*/

    /**
     * Default addresses column names to appropriate customer attribute code
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
     * Countries and regions
     *
     * array(
     *   [country_id_lowercased_1] => array(
     *     [region_code_lowercased_1]         => region_id_1,
     *     [region_default_name_lowercased_1] => region_id_1,
     *     ...,
     *     [region_code_lowercased_n]         => region_id_n,
     *     [region_default_name_lowercased_n] => region_id_n
     *   ),
     *   ...
     * )
     *
     * @var array
     */
    protected $_countryRegions = array();

    /**
     * Region ID to region default name pairs.
     *
     * @var array
     */
    protected $_regions = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');

        $this->addMessageTemplate(self::ERROR_ADDRESS_ID_IS_EMPTY, $helper->__('Customer address id is not specified'));
        $this->addMessageTemplate(self::ERROR_CUSTOMER_NOT_FOUND,
            $helper->__("Customer with such email and website code doesn't exist")
        );
        $this->addMessageTemplate(self::ERROR_INVALID_REGION, $helper->__('Region is invalid'));

        $this->_initCountryRegions()
            ->_initAttributes();
    }

    /**
     * Initialize country regions hash for clever recognition.
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Address
     */
    protected function _initCountryRegions()
    {
        foreach (Mage::getResourceModel('Mage_Directory_Model_Resource_Region_Collection') as $regionRow) {
            $countryNormalized = strtolower($regionRow['country_id']);
            $regionCode = strtolower($regionRow['code']);
            $regionName = strtolower($regionRow['default_name']);
            $this->_countryRegions[$countryNormalized][$regionCode] = $regionRow['region_id'];
            $this->_countryRegions[$countryNormalized][$regionName] = $regionRow['region_id'];
            $this->_regions[$regionRow['region_id']] = $regionRow['default_name'];
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

        if (empty($rowData[self::COLUMN_ADDRESS_ID])) {
            $this->addRowError(self::ERROR_ADDRESS_ID_IS_EMPTY, $rowNumber);
        } elseif (empty($rowData[self::COLUMN_WEBSITE])) {
            $this->addRowError(self::ERROR_WEBSITE_IS_EMPTY, $rowNumber);
        } elseif (empty($rowData[self::COLUMN_EMAIL])) {
            $this->addRowError(self::ERROR_EMAIL_IS_EMPTY, $rowNumber);
        } else {
            $email   = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(self::ERROR_INVALID_EMAIL, $rowNumber);
            } elseif (!isset($this->_websiteCodeToId[$website])) {
                $this->addRowError(self::ERROR_INVALID_WEBSITE, $rowNumber);
            } elseif (!$this->_getCustomerId($email, $website)) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            } else {
                // check simple attributes
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if (in_array($attributeCode, $this->_ignoredAttributes)) {
                        continue;
                    }
                    if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                        $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                    } elseif ($attributeParams['is_required']) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                    }
                }

                $countryRegions = isset($this->_countryRegions[strtolower($rowData['country_id'])])
                    ? $this->_countryRegions[strtolower($rowData['country_id'])]
                    : array();

                if (!empty($rowData['region'])
                    && !empty($countryRegions)
                    && !isset($countryRegions[strtolower($rowData['region'])])
                ) {
                    $this->addRowError(self::ERROR_INVALID_REGION, $rowNumber);
                }
            }
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

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
}
