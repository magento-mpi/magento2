<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option text type
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Option_Type_Text extends Magento_Catalog_Model_Product_Option_Type_Default
{
    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Constructor
     *
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_String $coreString
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_String $coreString,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_coreString = $coreString;
        parent::__construct($checkoutSession, $coreStoreConfig, $data);
    }

    /**
     * Validate user input for option
     *
     * @throws Magento_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Magento_Catalog_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = trim($this->getUserValue());

        // Check requires option to have some value
        if (strlen($value) == 0 && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            throw new Magento_Core_Exception(__('Please specify the product\'s required option(s).'));
        }

        // Check maximal length limit
        $maxCharacters = $option->getMaxCharacters();
        if ($maxCharacters > 0 && $this->_coreString->strlen($value) > $maxCharacters) {
            $this->setIsValid(false);
            throw new Magento_Core_Exception(__('The text is too long.'));
        }

        $this->setUserValue($value);
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && strlen($this->getUserValue()) > 0) {
            return $this->getUserValue();
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $value Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($value)
    {
        return $this->_coreData->escapeHtml($value);
    }
}
