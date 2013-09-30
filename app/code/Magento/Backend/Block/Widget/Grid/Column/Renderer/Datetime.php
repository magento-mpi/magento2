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
class Magento_Backend_Block_Widget_Grid_Column_Renderer_Datetime
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
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
                    self::$_format = $this->_locale->getDateTimeFormat(
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
        if ($data = $this->_getValue($row)) {
            $format = $this->_getFormat();
            try {
                $data = $this->_locale->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            catch (Exception $e)
            {
                $data = $this->_locale->date($data, Magento_Date::DATETIME_INTERNAL_FORMAT)->toString($format);
            }
            return $data;
        }
        return $this->getColumn()->getDefault();
    }
}
