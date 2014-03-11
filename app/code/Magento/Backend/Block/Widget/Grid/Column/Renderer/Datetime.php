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
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class Datetime
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Date format string
     *
     * @var string
     */
    protected static $_format = null;

    /**
     * Retrieve datetime format
     *
     * @return string|null
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = $this->_localeDate->getDateTimeFormat(
                        \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
                    );
                }
                catch (\Exception $e) {
                    $this->_logger->logException($e);
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
                $data = $this->_localeDate->date($data, \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            catch (\Exception $e)
            {
                $data = $this->_localeDate->date($data, \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
