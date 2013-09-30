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
 * Backend grid item renderer date
 */
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Date
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 160;
    /**
     * Date format string
     */
    protected static $_format = null;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($context, $data);
    }

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
                        Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM
                    );
                }
                catch (Exception $e) {
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
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $format = $this->_getFormat();
            try {
                if ($this->getColumn()->getGmtoffset()) {
                    $data = $this->_locale->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = $this->_locale->date($data, Zend_Date::ISO_8601, null, false)->toString($format);
                }
            }
            catch (Exception $e)
            {
                if ($this->getColumn()->getTimezone()) {
                    $data = $this->_locale->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
                } else {
                    $data = $this->_locale->date($data, null, null, false)->toString($format);
                }
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
