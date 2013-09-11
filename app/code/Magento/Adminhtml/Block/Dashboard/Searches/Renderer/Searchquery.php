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
 * Dashboard search query column renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
namespace Magento\Adminhtml\Block\Dashboard\Searches\Renderer;

class Searchquery
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(\Magento\Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if (\Mage::helper('Magento\Core\Helper\String')->strlen($value) > 30) {
            $value = '<span title="'. $this->escapeHtml($value) .'">'
                . $this->escapeHtml(\Mage::helper('Magento\Core\Helper\String')->truncate($value, 30)) . '</span>';
        }
        else {
            $value = $this->escapeHtml($value);
        }
        return $value;
    }
}
