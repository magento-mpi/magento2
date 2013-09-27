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
 * Dashboard Year-To-Date Month and Day starts Field Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Config_Form_Field_YtdStart extends Magento_Backend_Block_System_Config_Form_Field
{
    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_locale = $locale;
        parent::__construct($coreData, $context, $application, $data);
    }

    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $_months = array();
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = $this->_locale->date(mktime(null, null, null, $i))
                ->get(Zend_Date::MONTH_NAME);
        }

        $_days = array();
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0'.$i : $i;
        }

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $element->setName($element->getName() . '[]');

        $_monthsHtml = $element->setStyle('width:100px;')
            ->setValues($_months)
            ->setValue(isset($values[0]) ? $values[0] : null)
            ->getElementHtml();

        $_daysHtml = $element->setStyle('width:50px;')
            ->setValues($_days)
            ->setValue(isset($values[1]) ? $values[1] : null)
            ->getElementHtml();

        return sprintf('%s %s', $_monthsHtml, $_daysHtml);
    }
}
