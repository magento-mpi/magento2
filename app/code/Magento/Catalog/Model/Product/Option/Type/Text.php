<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Type;

/**
 * Catalog product option text type
 */
class Text extends \Magento\Catalog\Model\Product\Option\Type\DefaultType
{
    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper = null;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Escaper $escaper
     * @param \Magento\Stdlib\String $string
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Escaper $escaper,
        \Magento\Stdlib\String $string,
        array $data = array()
    ) {
        $this->_escaper = $escaper;
        $this->string = $string;
        parent::__construct($checkoutSession, $coreStoreConfig, $data);
    }

    /**
     * Validate user input for option
     *
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return $this
     * @throws \Magento\Core\Exception
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = trim($this->getUserValue());

        // Check requires option to have some value
        if (strlen($value) == 0 && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            throw new \Magento\Core\Exception(__('Please specify the product\'s required option(s).'));
        }

        // Check maximal length limit
        $maxCharacters = $option->getMaxCharacters();
        if ($maxCharacters > 0 && $this->string->strlen($value) > $maxCharacters) {
            $this->setIsValid(false);
            throw new \Magento\Core\Exception(__('The text is too long.'));
        }

        $this->setUserValue($value);
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @return string|null Prepared option value
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
        return $this->_escaper->escapeHtml($value);
    }
}
