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
 * Product options text type block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Block_Product_View_Options_Type_Date extends Magento_Catalog_Block_Product_View_Options_Abstract
{

    /**
     * Fill date and time options with leading zeros or not
     *
     * @var boolean
     */
    protected $_fillLeadingZeros = true;

    /**
     * Catalog product option type date
     *
     * @var Magento_Catalog_Model_Product_Option_Type_Date
     */
    protected $_catalogProductOptionTypeDate;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * Construct
     *
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Catalog_Model_Product_Option_Type_Date
     * $catalogProductOptionTypeDate
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Catalog_Model_Product_Option_Type_Date $catalogProductOptionTypeDate,
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_catalogProductOptionTypeDate = $catalogProductOptionTypeDate;
        parent::__construct($taxData, $coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return parent::_prepareLayout();
    }

    /**
     * Use JS calendar settings
     *
     * @return boolean
     */
    public function useCalendar()
    {
        return $this->_catalogProductOptionTypeDate->useCalendar();
    }

    /**
     * Date input
     *
     * @return string Formatted Html
     */
    public function getDateHtml()
    {
        if ($this->useCalendar()) {
            return $this->getCalendarDateHtml();
        } else {
            return $this->getDropDownsDateHtml();
        }
    }

    /**
     * JS Calendar html
     *
     * @return string Formatted Html
     */
    public function getCalendarDateHtml()
    {
        $option = $this->getOption();
        $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/date');

        $yearStart = $this->_catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->_catalogProductOptionTypeDate->getYearEnd();

        $calendar = $this->getLayout()
            ->createBlock('Magento_Core_Block_Html_Date')
            ->setId('options_'.$this->getOption()->getId().'_date')
            ->setName('options['.$this->getOption()->getId().'][date]')
            ->setClass('product-custom-option datetime-picker input-text')
            ->setImage($this->getViewFileUrl('Magento_Core::calendar.gif'))
            ->setDateFormat($this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT))
            ->setValue($value)
            ->setYearsRange($yearStart . ':' . $yearEnd);

        return $calendar->getHtml();
    }

    /**
     * Date (dd/mm/yyyy) html drop-downs
     *
     * @return string Formatted Html
     */
    public function getDropDownsDateHtml()
    {
        $fieldsSeparator = '&nbsp;';
        $fieldsOrder = $this->_catalogProductOptionTypeDate->getConfigData('date_fields_order');
        $fieldsOrder = str_replace(',', $fieldsSeparator, $fieldsOrder);

        $monthsHtml = $this->_getSelectFromToHtml('month', 1, 12);
        $daysHtml = $this->_getSelectFromToHtml('day', 1, 31);

        $yearStart = $this->_catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->_catalogProductOptionTypeDate->getYearEnd();
        $yearsHtml = $this->_getSelectFromToHtml('year', $yearStart, $yearEnd);

        $translations = array(
            'd' => $daysHtml,
            'm' => $monthsHtml,
            'y' => $yearsHtml
        );
        return strtr($fieldsOrder, $translations);
    }

    /**
     * Time (hh:mm am/pm) html drop-downs
     *
     * @return string Formatted Html
     */
    public function getTimeHtml()
    {
        if ($this->_catalogProductOptionTypeDate->is24hTimeFormat()) {
            $hourStart = 0;
            $hourEnd = 23;
            $dayPartHtml = '';
        } else {
            $hourStart = 1;
            $hourEnd = 12;
            $dayPartHtml = $this->_getHtmlSelect('day_part')
                ->setOptions(array(
                    'am' => __('AM'),
                    'pm' => __('PM')
                ))
                ->getHtml();
        }
        $hoursHtml = $this->_getSelectFromToHtml('hour', $hourStart, $hourEnd);
        $minutesHtml = $this->_getSelectFromToHtml('minute', 0, 59);

        return $hoursHtml . '&nbsp;<b>:</b>&nbsp;' . $minutesHtml . '&nbsp;' . $dayPartHtml;
    }

    /**
     * Return drop-down html with range of values
     *
     * @param string $name Id/name of html select element
     * @param int $from  Start position
     * @param int $to    End position
     * @param int $value Value selected
     * @return string Formatted Html
     */
    protected function _getSelectFromToHtml($name, $from, $to, $value = null)
    {
        $options = array(
            array('value' => '', 'label' => '-')
        );
        for ($i = $from; $i <= $to; $i++) {
            $options[] = array('value' => $i, 'label' => $this->_getValueWithLeadingZeros($i));
        }
        return $this->_getHtmlSelect($name, $value)
            ->setOptions($options)
            ->getHtml();
    }

    /**
     * HTML select element
     *
     * @param string $name Id/name of html select element
     * @return Magento_Core_Block_Html_Select
     */
    protected function _getHtmlSelect($name, $value = null)
    {
        $option = $this->getOption();

        $this->setSkipJsReloadPrice(1);

        // $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $require = '';
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setId('options_' . $this->getOption()->getId() . '_' . $name)
            ->setClass('product-custom-option datetime-picker' . $require)
            ->setExtraParams()
            ->setName('options[' . $option->getId() . '][' . $name . ']');

        $extraParams = 'style="width:auto"';
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $select->setExtraParams($extraParams);

        if (is_null($value)) {
            $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/' . $name);
        }
        if (!is_null($value)) {
            $select->setValue($value);
        }

        return $select;
    }

    /**
     * Add Leading Zeros to number less than 10
     *
     * @param int
     * @return string
     */
    protected function _getValueWithLeadingZeros($value)
    {
        if (!$this->_fillLeadingZeros) {
            return $value;
        }
        return $value < 10 ? '0'.$value : $value;
    }
}
