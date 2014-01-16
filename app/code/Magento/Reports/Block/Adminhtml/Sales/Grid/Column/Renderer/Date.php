<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid item renderer date
 */
namespace Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer;

class Date
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Date
{
    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    $localeCode = $this->_locale->getLocaleCode();
                    $localeData = new \Zend_Locale_Data;
                    switch ($this->getColumn()->getPeriodType()) {
                        case 'month' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'yM');
                            break;

                        case 'year' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'y');
                            break;

                        default:
                            self::$_format = $this->_locale->getDateFormat(
                                \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
                            );
                            break;
                    }
                }
                catch (\Exception $e) {

                }
            }
            $format = self::$_format;
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            switch ($this->getColumn()->getPeriodType()) {
                case 'month' :
                    $dateFormat = 'yyyy-MM';
                    break;
                case 'year' :
                    $dateFormat = 'yyyy';
                    break;
                default:
                    $dateFormat = \Magento\Stdlib\DateTime::DATE_INTERNAL_FORMAT;
                    break;
            }

            $format = $this->_getFormat();
            try {
                $data = ($this->getColumn()->getGmtoffset())
                    ? $this->_locale->date($data, $dateFormat)->toString($format)
                    : $this->_locale->date($data, \Zend_Date::ISO_8601, null, false)->toString($format);
            }
            catch (\Exception $e) {
                $data = ($this->getColumn()->getTimezone())
                    ? $this->_locale->date($data, $dateFormat)->toString($format)
                    : $this->_locale->date($data, $dateFormat, null, false)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
