<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Block\Adminhtml\Product\Bundle;

class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $rendered = parent::render($row);
        $link = '';
        if ($row->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $link = sprintf(
                '<a href="javascript:void(0)" class="product_to_add" id="productId_%s">%s</a>',
                $row->getId(),
                __('Select Items')
            );
        }
        return $rendered . $link;
    }
}
