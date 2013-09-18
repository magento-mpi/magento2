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
namespace Magento\Catalog\Model\Product\Option\Type;

class Text extends \Magento\Catalog\Model\Product\Option\Type\DefaultType
{
    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Helper\String $coreString
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Helper\String $coreString,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_coreString = $coreString;
        parent::__construct($coreStoreConfig, $data);
    }

    /**
     * Validate user input for option
     *
     * @throws \Magento\Core\Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = trim($this->getUserValue());

        // Check requires option to have some value
        if (strlen($value) == 0 && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            \Mage::throwException(__('Please specify the product\'s required option(s).'));
        }

        // Check maximal length limit
        $maxCharacters = $option->getMaxCharacters();
        if ($maxCharacters > 0 && $this->_coreString->strlen($value) > $maxCharacters) {
            $this->setIsValid(false);
            \Mage::throwException(__('The text is too long.'));
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
