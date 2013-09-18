<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Date grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 * @todo        date format
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Date extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    protected $_locale;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Context $context,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return $this;
    }

    public function getHtml()
    {
        $htmlId = $this->_coreData->uniqHash($this->_getHtmlId());
        $format = $this->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $html = '<div class="range" id="' . $htmlId . '_range"><div class="range-line date">'
            . '<input type="text" name="' . $this->_getHtmlName() . '[from]" id="' . $htmlId . '_from"'
                . ' value="' . $this->getEscapedValue('from') . '" class="input-text no-changes" placeholder="' . __('From') . '" '
                . $this->getUiId('filter', $this->_getHtmlName(), 'from') . '/>'
            . '</div>';
        $html .= '<div class="range-line date">'
            . '<input type="text" name="' . $this->_getHtmlName() . '[to]" id="' . $htmlId . '_to"'
                . ' value="' . $this->getEscapedValue('to') . '" class="input-text no-changes" placeholder="' . __('To') . '" '
                . $this->getUiId('filter', $this->_getHtmlName(), 'to') . '/>'
            . '</div></div>';
        $html .= '<input type="hidden" name="' . $this->_getHtmlName() . '[locale]"'
            . ' value="' . $this->getLocale()->getLocaleCode() . '"/>';
        $html .= '<script type="text/javascript">
            (function( $ ) {
                $("#' . $htmlId . '_range").dateRange({
                    dateFormat: "' . $format . '",
                    buttonImage: "' . $this->getViewFileUrl('images/grid-cal.gif') . '",
                    buttonText: "' . $this->escapeHtml(__('Date selector')) . '",
                    from: {
                        id: "' . $htmlId . '_from"
                    },
                    to: {
                        id: "' . $htmlId . '_to"
                    }
                })
            })(jQuery)
        </script>';
        return $html;
    }

    public function getEscapedValue($index=null)
    {
        $value = $this->getValue($index);
        if ($value instanceof \Zend_Date) {
            return $value->toString($this->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT));
        }
        return $value;
    }

    public function getValue($index=null)
    {
        if ($index) {
            if ($data = $this->getData('value', 'orig_' . $index)) {
                return $data;//date('Y-m-d', strtotime($data));
            }
            return null;
        }
        $value = $this->getData('value');
        if (is_array($value)) {
            $value['date'] = true;
        }
        return $value;
    }

    public function getCondition()
    {
        $value = $this->getValue();

        return $value;
    }

    public function setValue($value)
    {
        if (isset($value['locale'])) {
            if (!empty($value['from'])) {
                $value['orig_from'] = $value['from'];
                $value['from'] = $this->_convertDate($value['from'], $value['locale']);
            }
            if (!empty($value['to'])) {
                $value['orig_to'] = $value['to'];
                $value['to'] = $this->_convertDate($value['to'], $value['locale']);
            }
        }
        if (empty($value['from']) && empty($value['to'])) {
            $value = null;
        }
        $this->setData('value', $value);
        return $this;
    }

    /**
     * Retrieve locale
     *
     * @return \Magento\Core\Model\LocaleInterface
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = \Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param string $date
     * @param string $locale
     * @return \Zend_Date
     */
    protected function _convertDate($date, $locale)
    {
        try {
            $dateObj = $this->getLocale()->date(null, null, $locale, false);

            //set default timezone for store (admin)
            $dateObj->setTimezone(
                \Mage::app()->getStore()->getConfig(\Magento\Core\Model\LocaleInterface::XML_PATH_DEFAULT_TIMEZONE)
            );

            //set beginning of day
            $dateObj->setHour(00);
            $dateObj->setMinute(00);
            $dateObj->setSecond(00);

            //set date with applying timezone of store
            $dateObj->set($date, \Zend_Date::DATE_SHORT, $locale);

            //convert store date to default date in UTC timezone without DST
            $dateObj->setTimezone(\Mage::DEFAULT_TIMEZONE);

            return $dateObj;
        } catch (\Exception $e) {
            return null;
        }
    }
}
