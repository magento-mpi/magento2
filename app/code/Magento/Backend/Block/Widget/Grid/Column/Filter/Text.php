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
 * Text grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Text extends Magento_Backend_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $html = '<div class="field-100"><input type="text" name="'
            . $this->_getHtmlName()
            . '" id="'.$this->_getHtmlId()
            . '" value="'.$this->getEscapedValue()
            . '" class="input-text no-changes"'
            . $this->getUiId('filter', $this->_getHtmlName()) .  ' /></div>';
        return $html;
    }
}
