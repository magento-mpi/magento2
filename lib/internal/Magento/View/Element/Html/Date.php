<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Element\Html;

/**
 * Date element block
 */
class Date extends \Magento\View\Element\Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html  = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" ';
        $html .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
        $html .=
            '<script type="text/javascript">
            //<![CDATA[
            (function($) {
                $(document).ready(function(){
                    $("#' . $this->getId() . '").calendar({
                        showsTime: ' . ($this->getTimeFormat() ? 'true' : 'false') . ',
                        ' . ($this->getTimeFormat() ? ('timeFormat: "' . $this->getTimeFormat() . '",') : '') . '
                        dateFormat: "' . $this->getDateFormat() . '",
                        buttonImage: "' . $this->getImage() . '",
                        ' . ($calendarYearsRange ? 'yearRange: "' . $calendarYearsRange . '",' : '') . '
                        buttonText: "' . __('Select Date') . '"
                    })
                });
            })(jQuery)
            //]]>
            </script>';

        return $html;
    }

    /**
     * Convert special characters to HTML entities
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getFormat() && $this->getValue()) {
            return strftime($this->getFormat(), strtotime($this->getValue()));
        }
        return htmlspecialchars($this->getValue());
    }

    /**
     * Produce and return block's html output
     *
     * {@inheritdoc}
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
