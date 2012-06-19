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
abstract class Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_WEBSITE = '_website';
    /**#@-*/

    /**#@+
     * Error codes
     */
    const ERROR_WEBSITE_IS_EMPTY  = 'websiteIsEmpty';
    const ERROR_EMAIL_IS_EMPTY    = 'emailIsEmpty';
    const ERROR_INVALID_WEBSITE   = 'invalidWebsite';
    const ERROR_INVALID_EMAIL     = 'invalidEmail';
    const ERROR_VALUE_IS_REQUIRED = 'valueIsRequired';
    /**#@-*/

    /**
     * Existing customers information. In form of:
     *
     * [customer e-mail] => array(
     *    [website id 1] => customer_id 1,
     *    [website id 2] => customer_id 2,
     *           ...       =>     ...      ,
     *    [website id n] => customer_id n,
     * )
     *
     * @var array
     */
    protected $_customers = array();

    /**
     * Array of attribute codes which will be ignored in validation and import procedures.
     * For example, when entity attribute has own validation and import procedures
     * or just to deny this attribute processing.
     *
     * @var array
     */
    protected $_ignoredAttributes = array('website_id', 'store_id', 'country_id',  'region_id', 'default_billing',
        'default_shipping'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');

        $this->addMessageTemplate(self::ERROR_WEBSITE_IS_EMPTY, $helper->__('Website is not specified'));
        $this->addMessageTemplate(self::ERROR_EMAIL_IS_EMPTY, $helper->__('E-mail is not specified'));
        $this->addMessageTemplate(self::ERROR_INVALID_WEBSITE,
            $helper->__('Invalid value in Website column (website does not exists?)')
        );
        $this->addMessageTemplate(self::ERROR_INVALID_EMAIL, $helper->__('E-mail is invalid'));
        $this->addMessageTemplate(self::ERROR_VALUE_IS_REQUIRED,
            $helper->__("Required attribute '%s' has an empty value")
        );

        $this->_initCustomers()
            ->_initWebsites(true);
    }

    /**
     * Initialize existent customers data
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
     */
    protected function _initCustomers()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        foreach (Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection') as $customer) {
            $email = strtolower($customer->getEmail());
            if (!isset($this->_customers[$email])) {
                $this->_customers[$email] = array();
            }
            $this->_customers[$email][$customer->getWebsiteId()] = $customer->getId();
        }

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
            if (isset($this->_customers[$email][$websiteId])) {
                return $this->_customers[$email][$websiteId];
            }
        }

        return false;
    }
}
