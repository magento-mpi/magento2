<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Date range promo widget chooser
 * Currently works without localized format
 */
class Mage_Adminhtml_Block_Promo_Widget_Chooser_Daterange extends Mage_Core_Block_Template
{
    /**
     * HTML ID of the element that will obtain the joined chosen values
     *
     * @var string
     */
    protected $_targetElementId = '';

    /**
     * From/To values to be rendered
     *
     * @var array
     */
    protected $_rangeValues     = array('from' => '', 'to' => '');

    /**
     * Range string delimiter for from/to dates
     *
     * @var string
     */
    protected $_rangeDelimiter  = '...';

    /**
     * Render the chooser HTML
     * Target element should be set.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (empty($this->_targetElementId)) {
            return '';
        }

        $idSuffix = Mage::helper('Mage_Core_Helper_Data')->uniqHash();
        $form = new Varien_Data_Form();
        foreach (array(
            'from' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('From'),
            'to'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('To')) as $key => $label) {
            $id = "{$key}_{$idSuffix}";
            $element = new Varien_Data_Form_Element_Date(array(
                'format'   => Varien_Date::DATE_INTERNAL_FORMAT, // hardcode because hardcoded values delimiter
                'label'    => $label,
                'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
                'onchange' => "dateTimeChoose_{$idSuffix}()", // won't work through Event.observe()
                'value'    => $this->_rangeValues[$key],
            ));
            $element->setId($id);
            $form->addElement($element);
        }
        return $form->toHtml() . "<script type=\"text/javascript\">
            dateTimeChoose_{$idSuffix} = function() {
                $('{$this->_targetElementId}').value = $('from_{$idSuffix}').value + '{$this->_rangeDelimiter}' + $('to_{$idSuffix}').value;
            };
            </script>";
    }

    /**
     * Target element ID setter
     *
     * @param string $value
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Daterange
     */
    public function setTargetElementId($value)
    {
        $this->_targetElementId = trim($value);
        return $this;
    }

    /**
     * Range values setter
     *
     * @param string $from
     * @param string $to
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Daterange
     */
    public function setRangeValues($from, $to)
    {
        $this->_rangeValues = array('from' => $from, 'to' => $to);
        return $this;
    }

    /**
     * Range values setter, string implementation.
     * Automatically attempts to split the string by delimiter
     *
     * @param string $delimitedString
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Daterange
     */
    public function setRangeValue($delimitedString)
    {
        $split = explode($this->_rangeDelimiter, $delimitedString, 2);
        $from = $split[0]; $to = '';
        if (isset($split[1])) {
            $to = $split[1];
        }
        return $this->setRangeValues($from, $to);
    }

    /**
     * Range delimiter setter
     *
     * @param string $value
     * @return Mage_Adminhtml_Block_Promo_Widget_Chooser_Daterange
     */
    public function setRangeDelimiter($value)
    {
        $this->_rangeDelimiter = (string)$value;
        return $this;
    }
}
