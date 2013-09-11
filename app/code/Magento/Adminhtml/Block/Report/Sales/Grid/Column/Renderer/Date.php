<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid item renderer date
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Report\Sales\Grid\Column\Renderer;

class Date
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Date
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
                    $localeCode = \Mage::app()->getLocale()->getLocaleCode();
                    $localeData = new \Zend_Locale_Data;
                    switch ($this->getColumn()->getPeriodType()) {
                        case 'month' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'yM');
                            break;

                        case 'year' :
                            self::$_format = $localeData->getContent($localeCode, 'dateitem', 'y');
                            break;

                        default:
                            self::$_format = \Mage::app()->getLocale()->getDateFormat(
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
                    $dateFormat = \Magento\Date::DATE_INTERNAL_FORMAT;
                    break;
            }

            $format = $this->_getFormat();
            try {
                $data = ($this->getColumn()->getGmtoffset())
                    ? \Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : \Mage::getSingleton('Magento\Core\Model\LocaleInterface')->date($data, \Zend_Date::ISO_8601, null, false)->toString($format);
            }
            catch (\Exception $e) {
                $data = ($this->getColumn()->getTimezone())
                    ? \Mage::app()->getLocale()->date($data, $dateFormat)->toString($format)
                    : \Mage::getSingleton('Magento\Core\Model\LocaleInterface')->date($data, $dateFormat, null, false)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
