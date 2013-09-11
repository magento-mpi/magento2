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
 * Backend grid item renderer datetime
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class Datetime
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Date format string
     */
    protected static $_format = null;

    /**
     * Retrieve datetime format
     *
     * @return unknown
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = \Mage::app()->getLocale()->getDateTimeFormat(
                        \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
                    );
                }
                catch (\Exception $e) {
                    \Mage::logException($e);
                }
            }
            $format = self::$_format;
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($data = $this->_getValue($row)) {
            $format = $this->_getFormat();
            try {
                $data = \Mage::app()->getLocale()
                    ->date($data, \Magento\Date::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            catch (\Exception $e)
            {
                $data = \Mage::app()->getLocale()
                    ->date($data, \Magento\Date::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
