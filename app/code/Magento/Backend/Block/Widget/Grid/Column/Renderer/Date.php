<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

/**
 * Backend grid item renderer date
 */
class Date
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 160;

    /**
     * Date format string
     *
     * @var string
     */
    protected static $_format = null;

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
                    self::$_format = $this->_locale->getDateFormat(
                        \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
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
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $format = $this->_getFormat();
            try {
                if ($this->getColumn()->getGmtoffset()) {
                    $data = $this->_locale->date($data, \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = $this->_locale->date($data, \Zend_Date::ISO_8601, null, false)->toString($format);
                }
            }
            catch (\Exception $e)
            {
                if ($this->getColumn()->getTimezone()) {
                    $data = $this->_locale->date($data, \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = $this->_locale->date($data, null, null, false)->toString($format);
                }
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
