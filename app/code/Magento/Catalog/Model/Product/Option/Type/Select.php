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
 * Catalog product option select type
 */
class Select extends \Magento\Catalog\Model\Product\Option\Type\DefaultType
{
    /**
     * @var mixed
     */
    protected $_formattedOptionValue;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * Magento string lib
     *
     * @var \Magento\Stdlib\String
     */
    protected $string;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Stdlib\String $string
     * @param \Magento\Escaper $escaper
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Stdlib\String $string,
        \Magento\Escaper $escaper,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->string = $string;
        $this->_escaper = $escaper;
        parent::__construct($checkoutSession, $coreStoreConfig, $data);
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
        $value = $this->getUserValue();

        if (empty($value) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
            $this->setIsValid(false);
            throw new \Magento\Core\Exception(__('Please specify the product required option(s).'));
        }
        if (!$this->_isSingleSelection()) {
            $valuesCollection = $option->getOptionValuesByOptionId($value, $this->getProduct()->getStoreId())
                ->load();
            if ($valuesCollection->count() != count($value)) {
                $this->setIsValid(false);
                throw new \Magento\Core\Exception(__('Please specify the product required option(s).'));
            }
        }
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @throws \Magento\Core\Exception
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && $this->getUserValue()) {
            return is_array($this->getUserValue()) ? implode(',', $this->getUserValue()) : $this->getUserValue();
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        if ($this->_formattedOptionValue === null) {
            $this->_formattedOptionValue = $this->_escaper->escapeHtml(
                $this->getEditableOptionValue($optionValue)
            );
        }
        return $this->_formattedOptionValue;
    }

    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        return $this->getFormattedOptionValue($optionValue);
    }

    /**
     * Return currently unavailable product configuration message
     *
     * @return string
     */
    protected function _getWrongConfigurationMessage()
    {
        return __('Some of the selected item options are not currently available.');
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        $option = $this->getOption();
        $result = '';
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                $_result = $option->getValueById($_value);
                if ($_result) {
                    $result .= $_result->getTitle() . ', ';
                } else {
                    if ($this->getListener()) {
                        $this->getListener()->setHasError(true)->setMessage(
                            $this->_getWrongConfigurationMessage()
                        );
                        $result = '';
                        break;
                    }
                }
            }
            $result = $this->string->substr($result, 0, -2);
        } elseif ($this->_isSingleSelection()) {
            $_result = $option->getValueById($optionValue);
            if ($_result) {
                $result = $_result->getTitle();
            } else {
                if ($this->getListener()) {
                    $this->getListener()->setHasError(true)->setMessage(
                        $this->_getWrongConfigurationMessage()
                    );
                }
                $result = '';
            }
        } else {
            $result = $optionValue;
        }
        return $result;
    }

    /**
     * Parse user input value and return cart prepared value, i.e. "one, two" => "1,2"
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        $values = array();
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $value) {
                $value = trim($value);
                if (array_key_exists($value, $productOptionValues)) {
                    $values[] = $productOptionValues[$value];
                }
            }
        } elseif ($this->_isSingleSelection() && array_key_exists($optionValue, $productOptionValues)) {
            $values[] = $productOptionValues[$optionValue];
        }
        if (count($values)) {
            return implode(',', $values);
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        if (!$this->_isSingleSelection()) {
            return explode(',', $optionValue);
        }
        return $optionValue;
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param float $basePrice
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice)
    {
        $option = $this->getOption();
        $result = 0;

        if (!$this->_isSingleSelection()) {
            foreach(explode(',', $optionValue) as $value) {
                $_result = $option->getValueById($value);
                if ($_result) {
                    $result += $this->_getChargableOptionPrice(
                        $_result->getPrice(),
                        $_result->getPriceType() == 'percent',
                        $basePrice
                    );
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        break;
                    }
                }
            }
        } elseif ($this->_isSingleSelection()) {
            $_result = $option->getValueById($optionValue);
            if ($_result) {
                $result = $this->_getChargableOptionPrice(
                    $_result->getPrice(),
                    $_result->getPriceType() == 'percent',
                    $basePrice
                );
            } else {
                if ($this->getListener()) {
                    $this->getListener()->setHasError(true)->setMessage(
                        $this->_getWrongConfigurationMessage()
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Return SKU for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param string $skuDelimiter Delimiter for Sku parts
     * @return string
     */
    public function getOptionSku($optionValue, $skuDelimiter)
    {
        $option = $this->getOption();

        if (!$this->_isSingleSelection()) {
            $skus = array();
            foreach(explode(',', $optionValue) as $value) {
                $optionSku = $option->getValueById($value);
                if ($optionSku) {
                    $skus[] = $optionSku->getSku();
                } else {
                    if ($this->getListener()) {
                        $this->getListener()->setHasError(true)->setMessage(
                            $this->_getWrongConfigurationMessage()
                        );
                        break;
                    }
                }
            }
            $result = implode($skuDelimiter, $skus);
        } elseif ($this->_isSingleSelection()) {
            $result = $option->getValueById($optionValue);
            if ($result) {
                return $result->getSku();
            } else {
                if ($this->getListener()) {
                    $this->getListener()->setHasError(true)->setMessage(
                        $this->_getWrongConfigurationMessage()
                    );
                }
                return '';
            }
        } else {
            $result = parent::getOptionSku($optionValue, $skuDelimiter);
        }

        return $result;
    }

    /**
     * Check if option has single or multiple values selection
     *
     * @return boolean
     */
    protected function _isSingleSelection()
    {
        $single = array(
            \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN,
            \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO
        );
        return in_array($this->getOption()->getType(), $single);
    }
}
