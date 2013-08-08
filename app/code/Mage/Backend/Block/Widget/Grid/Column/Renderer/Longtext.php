<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Grid Renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Renderer_Longtext
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render contents as a long text
     *
     * Text will be truncated as specified in string_limit, truncate or 250 by default
     * Also it can be html-escaped and nl2br()
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $truncateLength = 250;
        // stringLength() is for legacy purposes
        if ($this->getColumn()->getStringLimit()) {
            $truncateLength = $this->getColumn()->getStringLimit();
        }
        if ($this->getColumn()->getTruncate()) {
            $truncateLength = $this->getColumn()->getTruncate();
        }
        $text = Mage::helper('Magento_Core_Helper_String')->truncate(parent::_getValue($row), $truncateLength);
        if ($this->getColumn()->getEscape()) {
            $text = $this->escapeHtml($text);
        }
        if ($this->getColumn()->getNl2br()) {
            $text = nl2br($text);
        }
        return $text;
    }
}
