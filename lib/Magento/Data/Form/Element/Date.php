<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Magento data selector form element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Date extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @var \Zend_Date
     */
    protected $_value;

    /**
     * @param \Magento\Escaper $escaper
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        \Magento\Escaper $escaper,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        parent::__construct($escaper, $factoryElement, $factoryCollection, $attributes);
        $this->setType('text');
        $this->setExtType('textfield');
        if (isset($attributes['value'])) {
            $this->setValue($attributes['value']);
        }
    }

    /**
     * If script executes on x64 system, converts large
     * numeric values to timestamp limit
     */
    protected function _toTimestamp($value)
    {

        $value = (int)$value;
        if ($value > 3155760000) {
            $value = 0;
        }

        return $value;
    }


    /**
     * Set date value
     * If \Zend_Date instance is provided instead of value, other params will be ignored.
     * Format and locale must be compatible with \Zend_Date
     *
     * @param mixed $value
     * @param string $format
     * @param string $locale
     * @return \Magento\Data\Form\Element\Date
     */
    public function setValue($value, $format = null, $locale = null)
    {
        if (empty($value)) {
            $this->_value = '';
            return $this;
        }
        if ($value instanceof \Zend_Date) {
            $this->_value = $value;
            return $this;
        }
        if (preg_match('/^[0-9]+$/', $value)) {
            $this->_value = new \Zend_Date($this->_toTimestamp($value));
            //$this->_value = new \Zend_Date((int)value);
            return $this;
        }
        // last check, if input format was set
        if (null === $format) {
            $format = \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT;
            if ($this->getInputFormat()) {
                $format = $this->getInputFormat();
            }
        }
        // last check, if locale was set
        if (null === $locale) {
            if (!$locale = $this->getLocale()) {
                $locale = null;
            }
        }
        try {
            $this->_value = new \Zend_Date($value, $format, $locale);
        } catch (\Exception $e) {
            $this->_value = '';
        }
        return $this;
    }

    /**
     * Get date value as string.
     * Format can be specified, or it will be taken from $this->getFormat()
     *
     * @param string $format (compatible with \Zend_Date)
     * @return string
     */
    public function getValue($format = null)
    {
        if (empty($this->_value)) {
            return '';
        }
        if (null === $format) {
            $format = $this->getDateFormat();
        }
        return $this->_value->toString($format);
    }

    /**
     * Get value instance, if any
     *
     * @return \Zend_Date
     */
    public function getValueInstance()
    {
        if (empty($this->_value)) {
            return null;
        }
        return $this->_value;
    }

    /**
     * Output the input field and assign calendar instance to it.
     * In order to output the date:
     * - the value must be instantiated (\Zend_Date)
     * - output format must be set (compatible with \Zend_Date)
     *
     * @throws \Exception
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-text');
        $dateFormat = $this->getDateFormat();
        $timeFormat = $this->getTimeFormat();
        if (empty($dateFormat)) {
            throw new \Exception('Output format is not specified. '
                . 'Please, specify "format" key in constructor, or set it using setFormat().');
        }

        $dataInit = 'data-mage-init="'
            . $this->_escape(json_encode(
                array(
                    'calendar' => array(
                        'dateFormat' => $dateFormat,
                        'showsTime' => !empty($timeFormat),
                        'timeFormat' => $timeFormat,
                        'buttonImage' => $this->getImage(),
                        'buttonText' => 'Select Date',
                        'disabled' => $this->getDisabled(),
                    )
                )
            ))
            . '"';

        $html = sprintf(
            '<input name="%s" id="%s" value="%s" %s %s />',
            $this->getName(),
            $this->getHtmlId(),
            $this->_escape($this->getValue()),
            $this->serialize($this->getHtmlAttributes()),
            $dataInit
        );
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
