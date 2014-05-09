<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Product\Downloads\Renderer;

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Purchases extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders Purchases value
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        if (($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }
        return __('Unlimited');
    }
}
